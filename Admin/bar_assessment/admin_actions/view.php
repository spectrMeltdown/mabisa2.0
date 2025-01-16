<?php
require 'admin_actions.php';
require_once '../db/db.php';

try {
    $admin = new Admin_Actions($pdo);

    $file_id = $_POST['file_id'];

    $admin->viewFile($file_id);
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
