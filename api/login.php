<?php

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
// ini_set('display_errors', 0); // Disable error display
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);    // Enable error logging
require_once '../db/db.php';
require_once 'logging.php';
require_once '../api/audit_log.php';
$log = new Audit_log($pdo);

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    writeLog('not a post!');
    throw new Exception('Invalid request method');
  }

  if (!isset($_POST['username'], $_POST['password'])) {
    writeLog('vars not read!');
    throw new Exception('Username or password not read! (Contact developer)');
  }

  // Example query
  $username = $_POST['username'];
  $password = $_POST['password'];
  $rememberMe = empty($_POST['rememberMe']) ? false : $_POST['rememberMe'];
  $sql = 'SELECT id, password FROM users WHERE username = :username LIMIT 1';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':username' => $username]);

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    writeLog('user not found!');
    throw new Exception('This account doesn\'t exist.');
  }
  if (empty($row['password'])) {
    writeLog('empty password');
    throw new Exception('Account login error. Try resetting the password.');
  }
  if (!password_verify($password, $row['password'])) {
    writeLog('failed in pass_verify');
    // NOTE: Intentionally saying username or password for security purposes
    throw new Exception('Username or password incorrect.');
  }

  $response = [
    'id' => (string)$row['id'],
  ];

  setcookie('id', (string)$row['id'], [
    'expires' => $rememberMe ? time() + (86400 * 14) : 0, // 2 weeks if user chose remember me
    'path' => '/',
    'secure' => true, // Ensure it's HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict', // Prevent CSRF
  ]);
  //logging 
  $log->userLog('Logged In');
  echo json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
  // http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
}
