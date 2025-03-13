<?php
require_once 'admin_actions.php';
require_once '../../../db/db.php';
$useAsFunction = true; // for send_notif to skip POST processing
require_once '../../../api/send_notif.php';
$admin = new Admin_Actions($pdo);
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'], $_POST['file_id'])) {
            $action = $_POST['action'];
            $file_id = $_POST['file_id'];
            $bid = $_POST['bid'];
            $iid = $_POST['iid'];
            $expand = $_POST['expand'];

            // get the user data
            $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1');
            $stmt->execute([':id' => $_COOKIE['id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            $title = $userData['role_name'] . ' ' . $action . 'd your submission.';
            $message = 'The ' . $userData['role_name']  . ' ' . $userData['full_name']  . ' ' . $action . 'd the submission with file ID ' . $file_id . '.';
            if ($action === 'approve') {
                $result = $admin->approve($file_id);
            } elseif ($action === 'decline') {
                $result = $admin->decline($file_id);
            } elseif ($action === 'revert') {
                $result = $admin->revert($file_id);
                // add 'ed' instead of just 'd'
                $title = $userData['role_name'] . ' ' . $action . 'ed your submission.';
                $message = 'The ' . $userData['role_name']  . ' ' . $userData['full_name']  . ' ' . $action . 'ed the submission with file ID ' . $file_id . '.';
            } else {
                throw new Exception('Invalid action specified.');
            }
            $notifResult = sendNotifBar($pdo, $userData['id'], $title, $message, $bid, $iid, $expand, ['assessment_comments_read', 'assessment_submissions_read'], $file_id);
            if ($notifResult) echo $notifResult;

            if ($result) {
                echo "<script>
                alert('" . $action . "d successfully');
                let url = new URL(document.referrer);
                url.searchParams.set('expand', '#" . $expand . "' );
                location.href = (url.toString() + '#" . $bid . $iid . "');
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
