<?php
require_once '../comments.php';
require_once '../../../db/db.php';
$useAsFunction = true; // for send_notif to skip POST processing
require_once '../../../api/send_notif.php';
require_once '../../../api/logging.php';

$comments = new Comments($pdo);

if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');

$file_id = $_POST['file_id'] ?? '';
$name = $_POST['name'] ?? '';
$commentText = $_POST['commentText'] ?? '';
$bid = $_POST['bid'] ?? '';
$iid = $_POST['iid'] ?? '';
$expand = $_POST['expand'];
// writeLog('bid was ' . $bid);
// writeLog('iid was ' . $iid);

if (!empty($file_id) && !empty($name) && !empty($commentText)) {
    $result = $comments->add_comment($file_id, $name, $commentText);

    // get the user data
    $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
    $stmt->execute([':id' => $_COOKIE['id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // send notif to all relevant users
    $notifResult = sendNotifBar($pdo, $userData['id'], ($userData['role_name'] . ' commented on your submission'), 'The ' . $userData['role_name'] . ' ' . $userData['full_name'] . ' commented File ID ' . $file_id . '.', (int)$bid, (int)$iid, $expand, ['assessment_comments_read', 'assessment_submissions_read'], $file_id);
    if ($notifResult) {
        writeLog($notifResult);
    }
    writeLog('expand');
    writeLog($expand);
    echo "<script>
        let url = new URL(document.referrer);
                url.searchParams.set('expand', '" . $expand . "' );
                location.href = (url.toString() + '#" . $bid . $iid . "');
    </script>";
    exit;
    // header('Content-Type: application/json');
    // echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'All parameters (file_id, name, commentText) are required'], JSON_PRETTY_PRINT);
}
