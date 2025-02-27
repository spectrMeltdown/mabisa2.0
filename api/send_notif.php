<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once 'has_permissions.php';

function sendNotif(\PDO $pdo, string $creatorID, string $title, string $message): ?string
{
  writeLog('IN SEND NOTIF');
  try {
    $sql = '
  SELECT id FROM users WHERE id != :creator_id
';
    $params = [':creator_id' => $creatorID];
    // get all roles with the assessment_comments_read && assessment_submissions_read permissions, excluding the creator of the notif
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();

    // send notif for all user ids with the roles
    foreach ($users as $user) {
      $pdo->beginTransaction();
      $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)');
      $stmt->execute([
        ':user_id' => $user['id'],
        ':title' => $title,
        ':message' => $message
      ]);
      $pdo->commit();
    }
    return null;
  } catch (\Throwable $th) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    writeLog($th);
    return $th;
  }
  return null;
}

function sendNotifBar(\PDO $pdo, string $creatorID, string $title, string $message, int|null $bid = null, int|null $iid = null): ?string
{
  writeLog('IN SEND NOTIF BAR');
  try {
    $sql = '';
    $params = [':creator_id' => $creatorID];

    // if both barangay and indicator ids are provided
    if (!empty($bid) && !empty($iid)) {
      $sql = '
  SELECT urb.user_id as uid, p.*
  FROM user_roles_barangay urb
  JOIN permissions p ON urb.permission_id = p.id
  WHERE p.assessment_comments_read = 1 AND p.assessment_submissions_read = 1 
  AND urb.barangay_id = :bid
  AND urb.indicator_id = :iid
  AND urb.user_id != :creator_id
';
      $params[':bid'] = $bid;
      $params[':iid'] = $iid;
    }

    // barangay-wide notif
    else if (!empty($bid)) {
      $sql = '
      SELECT urb.user_id as uid, p.*
      FROM user_roles_barangay urb
      JOIN permissions p ON urb.permission_id = p.id
      WHERE p.assessment_comments_read = 1 AND p.assessment_submissions_read = 1 
      AND urb.barangay_id = :bid
      AND urb.user_id != :creator_id
    ';
      $params[':bid'] = $bid;
    }

    // all users notif
    else {
      $sql = '
      SELECT urb.user_id as uid, p.*
      FROM user_roles_barangay urb
      JOIN permissions p ON urb.permission_id = p.id
      WHERE p.assessment_comments_read = 1 AND p.assessment_submissions_read = 1 
      AND urb.user_id != :creator_id
    ';
    }

    if (empty($sql)) throw new Exception('sql empty!');
    // get all roles with the assessment_comments_read && assessment_submissions_read permissions, excluding the creator of the notif
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    writeLog('Users are: ');
    writeLog($users);

    // remove duplicates
    $users = array_unique($users, SORT_REGULAR);

    // send notif for all user ids with the roles
    $pdo->beginTransaction();
    foreach ($users as $user) {
      $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)');
      $stmt->execute([
        ':user_id' => $user['uid'],
        ':title' => $title,
        ':message' => $message
      ]);
    }
    $pdo->commit();
    return null;
  } catch (\Throwable $th) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    writeLog($th);
    return $th;
  }
  return null;
}

if (isset($useAsFunction)) return;
try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  // redirect if logged out
  if (empty($_COOKIE['id'])) {
    header('location: ' . $_SERVER['HTTP_HOST'] . '/Admin/logged_out');
    exit;
  }

  if (empty($_POST['creator_id']) || empty($_POST['title']) || empty($_POST['message'])) {
    throw new Exception('All parameters (creator_id, title, message) are required');
  }

  /** @var string */
  $title = trim($_POST['creator_id']);
  /** @var string */
  $creatorID = trim($_POST['title']);
  /** @var string */
  $message = trim($_POST['message']);
  /** @var string */
  $bid = empty($_POST['bid']) ? null : $_POST['bid'];
  /** @var string */
  $iid = empty($_POST['iid']) ? null : $_POST['iid'];

  global $pdo;
  sendNotifBar($pdo, $creatorID, $title, $message, (int)$bid, (int)$iid);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
