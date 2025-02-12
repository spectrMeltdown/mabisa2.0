<?php
require 'admin_actions.php';
require_once '../../../db/db.php';
$admin = new Admin_Actions($pdo);
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'], $_POST['file_id'])) {
            $action = $_POST['action'];
            $file_id = $_POST['file_id'];

            if ($action === 'approve') {
                $result = $admin->approve($file_id);
            } elseif ($action === 'decline') {
                $result = $admin->decline($file_id);
            } elseif ($action === 'revert') {
                $result = $admin->revert($file_id);
            } else {
                throw new Exception('Invalid action specified.');
            }

            if ($result) {
                echo "<script>
                alert('" . $action . "d successfully');
                window.location.href = document.referrer;
                </script>";

            } else {
                echo "Failed to perform action '$action'.";
            }
        } else {
            throw new Exception('Invalid form data.');
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}