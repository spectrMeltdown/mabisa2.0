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
    return (string)$th;
  }
  return null;
}

function sendNotifBar(\PDO $pdo, string $creatorID, string $title, string $message, int|null $bid = null, int|null $iid = null, ?string $expand = null, array $perms = [], string $file_id = ''): ?string
{
  writeLog('IN SEND NOTIF BAR');
  writeLog($bid);
  writeLog($iid);
  try {
    $sql = 'SELECT urb.user_id as uid, p.*
  FROM user_roles_barangay urb
  JOIN permissions p ON urb.permission_id = p.id
  WHERE ';
    $params = [':creator_id' => (int)$creatorID];

    // construct the where statement from the permissions provided
    foreach ($perms as $perm) {
      $sql .= ' p.' . $perm .  ' = 1 AND';
    }

    // remove trailing AND
    $sql = preg_replace('/AND\s*$/', '', $sql);

    // if both barangay and indicator ids are provided
    if (!empty($bid) && !empty($iid)) {
      writeLog('bid and iid');

      $sql .= ' 
      AND urb.barangay_id = :bid
  AND urb.indicator_id = :iid
  AND urb.user_id != :creator_id
';
      $params[':bid'] = (int)$bid;
      $params[':iid'] = (int)$iid;
    }

    // barangay-wide notif
    else if (!empty($bid)) {
      writeLog('bid only');
      $sql = '
      SELECT urb.user_id as uid, p.*
      FROM user_roles_barangay urb
      JOIN permissions p ON urb.permission_id = p.id
    ';
      $sql .= ' 
      AND urb.barangay_id = :bid
      AND urb.user_id != :creator_id';

      $params[':bid'] = (int)$bid;
    }
    // all users notif
    else {
      throw new Exception('NO IID & BID');
    }

    if (empty($sql)) throw new Exception('sql empty!');
    // get all roles with the assessment_comments_read && assessment_submissions_read permissions, excluding the creator of the notif
    writeLog($sql);
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
      $sql = 'INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)';
      $params = [
        ':user_id' => $user['uid'],
        ':title' => $title,
        ':message' => $message
      ];
      writeLog('special vars');
      writeLog($bid);
      writeLog($iid);
      writeLog($expand);
      if (!empty($bid) &&  !empty($iid) && !empty($expand)) {
        $sql = 'INSERT INTO notifications (user_id, title, message, file_link) VALUES (:user_id, :title, :message, :file_link)';
        $params[':file_link'] = 'http://localhost/mabisa2.0/Admin/bar_assessment/show_bar_response.php?' . http_build_query([
          'barangay_id' => $bid,
          'file-id' => $file_id,
          'expand' => '#' . $expand
        ]) . '#' . urlencode($bid . $iid);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
      }
    }
    $pdo->commit();
    return null;
  } catch (\Throwable $th) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    writeLog($th);
    return (string)$th;
  }
  return null;
}

// if (isset($useAsFunction)) return;
// try {
//   if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
//   // redirect if logged out
//   if (empty($_COOKIE['id'])) {
//     header('location: ' . $_SERVER['HTTP_HOST'] . '/Admin/logged_out');
//     exit;
//   }

//   if (empty($_POST['creator_id']) || empty($_POST['title']) || empty($_POST['message'])) {
//     throw new Exception('All parameters (creator_id, title, message) are required');
//   }

//   /** @var string */
//   $title = trim($_POST['creator_id']);
//   /** @var string */
//   $creatorID = trim($_POST['title']);
//   /** @var string */
//   $message = trim($_POST['message']);
//   /** @var string */
//   $bid = empty($_POST['bid']) ? null : $_POST['bid'];
//   /** @var string */
//   $iid = empty($_POST['iid']) ? null : $_POST['iid'];
//   /** @var array */
//   $perms = empty($_POST['perms']) ? null : $_POST['perms'];

//   global $pdo;
//   sendNotifBar($pdo, $creatorID, $title, $message, (int)$bid, (int)$iid, $perms);
// } catch (\Throwable $th) {
//   http_response_code(500);
//   $message = $th->getMessage();
//   writeLog($message);
//   echo json_encode($message, JSON_PRETTY_PRINT);
// }
