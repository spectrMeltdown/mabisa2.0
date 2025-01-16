<?php
require 'user_actions.php';
require_once '../db/db.php';

try {

    $user = new User_Actions($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(
        $_POST['file_id'],

    )) {
        $file_id = $_POST['file_id'];


        try {
            $delete = $user->deleteFile($file_id);
        } catch (Exception $e) {
            echo "<script>alert('Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid request. Missing required parameters.'); window.history.back();</script>";
    }
} catch (PDOException $e) {
    echo "<p class='text-danger'>Database connection failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    exit;
}
