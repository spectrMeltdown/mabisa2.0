<?php
require 'admin_actions.php';
require_once '../../../db/db.php';

try {
    $admin = new Admin_Actions($pdo);

    if (!isset($_GET['file_id'])) {
        die("Error: File ID not provided.");
    }

    $file_id = $_GET['file_id'];
    $file_path = $admin->viewFile($file_id);

    // Redirect to file location
    header("Location: " . $file_path);
    exit;
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
