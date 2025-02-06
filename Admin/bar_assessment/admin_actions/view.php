
<?php
require 'admin_actions.php';
require_once '../../../db/db.php';

try {
    $admin = new Admin_Actions($pdo);

    // Fetch file_id from GET instead of POST
    if (!isset($_GET['file_id'])) {
        die("Error: File ID not provided.");
    }

    $file_id = $_GET['file_id']; // Retrieve file ID from the URL

    $admin->viewFile($file_id); // Call the function to display the file
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
