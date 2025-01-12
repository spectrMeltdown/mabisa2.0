<?php
require 'user_actions.php';
require_once '../../../db/db.php';

try {

    $user = new User_Actions($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset(
        $_POST['barangay_id'], 
        $_POST['req_keyctr'], 
        $_POST['desc_ctr'], 
        $_POST['indicator_code'], 
        $_POST['reqs_code']
    )) {
        $barangay_id = $_POST['barangay_id'];
        $req_keyctr = $_POST['req_keyctr'];
        $desc_ctr = $_POST['desc_ctr'];
        $indicator_code = $_POST['indicator_code'];
        $reqs_code = $_POST['reqs_code'];

        try {
            $delete = $user->deleteFile($barangay_id, $req_keyctr, $desc_ctr, $indicator_code, $reqs_code);

          
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
?>
