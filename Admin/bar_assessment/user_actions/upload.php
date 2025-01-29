<?php
require 'user_actions.php';
require_once '../../../db/db.php';


$barangayAssessment = new User_Actions($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    $barangay_id = $_POST['barangay_id'];
    $criteria_keyctr = $_POST['criteria_keyctr'];
   

    $filePath = $_FILES['file']['tmp_name'];
    $fileContent = file_get_contents($filePath);

    $success = $barangayAssessment->uploadFile($barangay_id, $criteria_keyctr, $fileContent);

    if ($success) {
        echo "<script>
            alert('File uploaded successfully!');
            window.location.href = document.referrer;
        </script>";
        exit;
    } else {
        echo "<script>alert('Failed to upload the file.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
