<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
// require_once '../models/user_model.php';
// require_once '../models/role_model.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
  }

  if (!isset($_POST['username'], $_POST['password'])) {
    throw new Exception('Username or password not read!');
  }

  // Example query
  $username = $_POST['username'];
  $password = $_POST['password'];
  $rememeberMe = $_POST['rememberMe'];
  $sql = 'SELECT id FROM user_policy WHERE username=:username and password=:password LIMIT 1';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT)]);

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    throw new Exception('User not found');
  }

  $response = [
    'id' => (string)$row['id'],
  ];

  setcookie('id', $row['id'], [
    'expires' => $rememeberMe ? time() + (86400 * 14) : 0, // 2 weeks if user chose remember me
    'path' => '/',
    'secure' => true, // Ensure it's HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict', // Prevent CSRF
  ]);

  echo json_encode($response);
} catch (Exception $e) {
  http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()]);
}
