<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'user_actions.php';
require_once '../../../db/db.php';
require_once '../../../api/audit_log.php';

header('Content-Type: application/json');

if (isset($_COOKIE['id'])) {
    $userId = $_COOKIE['id'];

    $sql = "SELECT * FROM users WHERE id = :id";
    $query = $pdo->prepare($sql);
    $query->execute(['id' => $userId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
} else {
    echo json_encode(['success' => false, 'message' => 'User ID not found in cookies.']);
    exit;
}

try {
    $userActions = new User_Actions($pdo);
    $log = new Audit_log($pdo);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        $data = $_POST;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['file_id'])) {
        $file_id = $data['file_id'];

        try {
            $delete = $userActions->deleteFile($file_id);
            if ($delete) {
                if ($user) {
                    $log->userLog($userId, $user['username'], "Deleted file with ID: $file_id");
                }
                echo json_encode(['success' => true, 'message' => 'File deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'File deletion failed.']);
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
