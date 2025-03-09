<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'user_actions.php';
require_once '../../../db/db.php';
require_once '../../../api/audit_log.php';
require_once '../../../api/logging.php';

header('Content-Type: application/json');
writeLog('IN VALIDATE');
try {
    $userActions = new User_Actions($pdo);
    $log = new Audit_log($pdo);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        $data = $_POST;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['barangay_id'])) {
        $barangay_id = $data['barangay_id'];
        $isReverse = !empty($data['isReverse']) ? $data['isReverse'] : null;
        writeLog($isReverse);

        try {
            $stmt = $pdo->prepare('UPDATE barangay_assessment SET is_ready = ' . (!empty($isReverse) ? '0' : '1') . ' WHERE barangay_id = :bid');
            $validate = $stmt->execute(['bid' => $barangay_id]);

            if ($validate) {
                if (!empty($isReverse)) {
                    $log->userLog("Barangay ID $barangay_id has finished validation");
                    echo json_encode(['success' => true, 'message' => 'Finished validation.', 'isReverse' => 'yes']);
                } else {
                    $log->userLog("Barangay ID $barangay_id has been submitted for validation");
                    echo json_encode(['success' => true, 'message' => 'Submitted for validation.']);
                }
            } else {
                $message = 'Submission failed.';
                if (!empty($isReverse)) $message = 'Update failed.';
                echo json_encode(['success' => false, 'message' => $message]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Missing required parameters.', 'received_data' => $data]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
