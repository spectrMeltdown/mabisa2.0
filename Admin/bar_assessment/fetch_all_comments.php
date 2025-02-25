<?php
require_once './comments.php';
require_once '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barangay_id'])) {
    $barangay_id = $_POST['barangay_id'];
    $comments = new Comments($pdo);
    $allComments = $comments->show_all_comments($barangay_id);

    if (!empty($allComments)) {
        foreach ($allComments as $comment) {
            echo "<div class='comment-item mb-2'>
                <strong>" . htmlspecialchars($comment['name']) . " (File ID: " . htmlspecialchars($comment['file_id']) . "):</strong>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <small class='text-muted'>" . date('F j, Y, g:i a', $comment['timestamp']) . "</small>
            </div>";
        }
    } else {
        echo "<p>No comments found.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>