<?php
require_once '../comments.php';
require_once '../../../db/db.php';

$comments = new Comments($pdo);

$file_id = $_POST['file_id'] ?? '';
$name = $_POST['name'] ?? '';
$commentText = $_POST['commentText'] ?? '';

if (!empty($file_id) && !empty($name) && !empty($commentText)) {
    $result = $comments->add_comment($file_id, $name, $commentText);

    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'All parameters (file_id, name, commentText) are required'], JSON_PRETTY_PRINT);
}
