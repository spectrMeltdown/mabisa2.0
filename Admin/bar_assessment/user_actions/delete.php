<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'user_actions.php';
require_once '../../../db/db.php';

header('Content-Type: application/json');

try {
    $user = new User_Actions($pdo);

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        $data = $_POST;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['file_id'])) {
        $file_id = $data['file_id'];

        try {
            $delete = $user->deleteFile($file_id);
            echo json_encode(['success' => true, 'message' => 'File deleted successfully.']);
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
