<?php
require_once './comments.php';
require_once '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barangay_id'])) {
    $barangay_id = $_POST['barangay_id'];
    $comments = new Comments($pdo);
    $allComments = $comments->show_all_comments($barangay_id);

    if (!empty($allComments)) {
        $groupedComments = [];

        foreach ($allComments as $comment) {
            $groupedComments[$comment['file_id']][] = $comment;
        }

        foreach ($groupedComments as $file_id => $comments) {
            echo "<div class='file-comments-container mb-4 p-3 border rounded bg-white shadow-sm'>";
            echo "<h6 class='font-weight-bold bg-primary text-white p-2 rounded'>Comments for File ID: " . htmlspecialchars($file_id) . "</h6>";
            foreach ($comments as $comment) {
                echo "<div class='comment-item mb-2 p-2 border-bottom'>";
                echo "<strong>" . htmlspecialchars($comment['name']) . ":</strong>";
                echo "<p class='mb-1'>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>";
                echo "<small class='text-muted'>" . date('F j, Y, g:i a', $comment['timestamp']) . "</small>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No comments Yet.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
