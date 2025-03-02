<?php
require_once 'user_actions.php';
require_once '../../../api/logging.php';
require_once '../../../db/db.php';
require_once '../../../api/audit_log.php';
$useAsFunction = true;
require_once '../../../api/send_notif.php';

header('Content-Type: application/json');

$barangayAssessment = new User_Actions($pdo);
$log = new Audit_log($pdo);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
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

            $log->userLog("Uploaded file: $fileName for Barangay ID: $barangay_id, Criteria: $criteria_keyctr");

            // get the user data
            $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
            $stmt->execute([':id' => $_COOKIE['id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            // send notif to all relevant users
            $notifResult = sendNotifBar($pdo, $userData['id'], ($userData['role_name'] . ' commented on your submission'), 'The ' . $userData['role_name'] . ' ' . $userData['full_name'] . ' commented File ID ' . $file_id . '.', (int)$bid, (int)$iid, ['assessment_comments_read', 'assessment_submissions_read']);
            if ($notifResult) {
                writeLog($notifResult);
            }

            writeLog('upload success');
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully.']);
            exit;
        } else {
            unlink($filePath);
            writeLog('Failed to save file in db');
            echo json_encode(['success' => false, 'message' => 'Failed to save file information in the database.']);
            exit;
        }
    } else {
        writeLog('Failed to upload');
        echo json_encode(['success' => false, 'message' => 'Failed to upload the file.']);
        exit;
    }
} else {
    writeLog('Invalid request');
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}
