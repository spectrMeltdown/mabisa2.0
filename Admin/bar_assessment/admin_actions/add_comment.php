<?php
require_once '../comments.php';
require_once '../../../db/db.php';
$useAsFunction = true; // for send_notif to skip POST processing
require_once '../../../api/send_notif.php';
require_once '../../../api/logging.php';

$comments = new Comments($pdo);

$file_id = $_POST['file_id'] ?? '';
$name = $_POST['name'] ?? '';
$commentText = $_POST['commentText'] ?? '';
$bid = $_POST['bid'] ?? '';
$iid = $_POST['iid'] ?? '';
writeLog('bid was ' . $bid);
writeLog('iid was ' . $iid);

if (!empty($file_id) && !empty($name) && !empty($commentText)) {
    $result = $comments->add_comment($file_id, $name, $commentText);

    // get the user data
    $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
    $stmt->execute([':id' => $_COOKIE['id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // send notif to all relevant users
    $notifResult = sendNotifBar($pdo, $userData['id'], ($userData['role_name'] . ' commented on your submission'), 'The ' . $userData['role_name'] . ' ' . $userData['full_name'] . ' commented File ID ' . $file_id . '.', (int)$bid, (int)$iid);
    if ($notifResult) writeLog($notifResult);
    echo "<script>
        window.location.href = document.referrer;
    </script>";
    exit;
    // header('Content-Type: application/json');
    // echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'All parameters (file_id, name, commentText) are required'], JSON_PRETTY_PRINT);
}
