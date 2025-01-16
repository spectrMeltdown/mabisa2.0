<?php
require_once './comments.php';
require_once '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id'];
    $comments = new Comments($pdo);
    $allComments = $comments->show_comments($file_id);

    if (!empty($allComments)) {
        foreach ($allComments as $comment) {
            echo "
            <div class='comment-item mb-2'>
                <strong>" . htmlspecialchars($comment['name']) . ":</strong>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <small class='text-muted'>" . date('F j, Y, g:i a', $comment['timestamp']) . "</small>
            </div>";
        }
    } else {
        echo "<p>No comments yet. Be the first to comment!</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
