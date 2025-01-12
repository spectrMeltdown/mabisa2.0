<?php
require '../comments.php'; 
require_once '../../../db/db.php';

$comments = new Comments($pdo);

$barangay = $_POST['barangay'] ?? '';
$name = $_POST['name'] ?? '';
$commentText = $_POST['commentText'] ?? '';

if (!empty($barangay) && !empty($name) && !empty($commentText)) {
    $result = $comments->add_comment($barangay, $name, $commentText);

    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'All parameters (barangay, name, commentText) are required']);
}
