<?php
require_once 'user_actions.php';
require_once '../../../api/logging.php';
require_once '../../../db/db.php';
require_once '../../../api/audit_log.php';
$useAsFunction = true;
require_once '../../../api/send_notif.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$barangayAssessment = new User_Actions($pdo);
$log = new Audit_log($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if (!isset($_POST['barangay_id'], $_POST['criteria_keyctr'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    if (!isset($_COOKIE['id'])) {
        echo json_encode(['success' => false, 'message' => 'User ID cookie not found.']);
        exit;
    }

    $barangay_id = $_POST['barangay_id'];
    $criteria_keyctr = $_POST['criteria_keyctr'];

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];

    // Define upload directory
    $uploadDir = '../../files/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . uniqid() . "_" . basename($fileName);

    if (move_uploaded_file($fileTmpPath, $filePath)) {
        $success = $barangayAssessment->uploadFile($barangay_id, $criteria_keyctr, $filePath, $fileName);

        if ($success) {
            $file_id = $pdo->lastInsertId(); // Retrieve last inserted ID
            
              $stmt = $pdo->prepare("
          SELECT min.reqs_code 
FROM maintenance_criteria_setup cri
INNER JOIN maintenance_area_mininumreqs min ON min.keyctr = cri.minreqs_keyctr
WHERE cri.keyctr = ?;

        ");
            $stmt->execute([$criteria_keyctr]);
            $icode = $stmt->fetchColumn();

            $log->userLog("Uploaded file: $fileName for Barangay ID: $barangay_id, Indicator: $icode");

            // Fetch user data
            $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
            $stmt->execute([':id' => $_COOKIE['id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                $iid = (int)$_POST['iid'];
                $expand = $_POST['expand'];
                $notifResult = sendNotifBar(
                    $pdo,
                    $userData['id'],
                    ($userData['role_name'] . ' uploaded a new submission'),
                    'The ' . $userData['role_name'] . ' ' . $userData['full_name'] . ' uploaded File ID ' . $file_id . '.',
                    $barangay_id,
                    $iid,
                    $expand,
                    ['assessment_comments_read', 'assessment_submissions_read'], $file_id
                );

                if ($notifResult) {
                    writeLog($notifResult);
                }
            }

            writeLog('upload success');
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.']);
        } else {
            unlink($filePath);
            writeLog('Failed to save file in db');
            echo json_encode(['success' => false, 'message' => 'Failed to save file information in the database.']);
        }
    } else {
        writeLog('Failed to upload');
        echo json_encode(['success' => false, 'message' => 'Failed to upload the file.']);
    }
} else {
    writeLog('Invalid request');
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
exit;
