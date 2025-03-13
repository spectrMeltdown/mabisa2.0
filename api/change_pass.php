<?php

declare(strict_types=1);
ini_set('display_errors', 0); // Disable error display
ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);    // Enable error logging
require_once 'logging.php';
require_once '../db/db.php';
require_once '../api/audit_log.php';

try {
  writeLog('IN CHANGE PASS');
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method');
  if (empty($_POST['newPass'])) throw new Exception('New password cannot be empty.');
  if (empty($_POST['oldPass'])) throw new Exception('Current password cannot be empty.');

  // get variables
  $newPass = trim($_POST['newPass']);
  $oldPass = trim($_POST['oldPass']);

  // check if old password matches saved password, throw exception if not
  $stmt = $pdo->prepare('SELECT username, password FROM users WHERE id = ? LIMIT 1');
  $execResult = $stmt->execute([$_COOKIE['id']]);
  $uData = $stmt->fetch();
  writeLog('user data is: ');
  writeLog($uData);
  $curPass = $uData['password'];
  if (!($execResult && password_verify($oldPass, $curPass))) throw new Exception('Current password is incorrect.');

  // check if old pass matches new pass, throw exception
  if ($newPass == $oldPass) throw new Exception('Current and New password cannot be the same.');

  // update the password
  $pdo->beginTransaction();
  $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
  $stmt->execute([password_hash($newPass, PASSWORD_BCRYPT), $_COOKIE['id']]);
  $pdo->commit();

  //logging
  $log = new Audit_log($pdo, $row['id']);
  $log->userLog('User ' . $uData['username'] . ' changed password.');
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500); // Set HTTP 500 response code
  writeLog($e);
  echo json_encode($e->getMessage(), JSON_PRETTY_PRINT);
}
