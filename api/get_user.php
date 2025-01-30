<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
require_once 'logging.php';
require_once 'get_role_name.php';
require_once (isset($isInFolder) ? '../' : '') . '../db/db.php';

$isGetMethod = $_SERVER['REQUEST_METHOD'] === 'GET';

function getUser(string $id)
{
  global $pdo;
  global $isGetMethod;
  global $customUserID;

  if (!isset($_GET['id']) && !$isGetMethod) {
    throw new Exception('No id provided!');
  }

  if ($id === 'self') {
    // echo "was a cookie id!";
    $id = $_COOKIE['id'];
  }

  $sql = "SELECT * FROM users WHERE id=:id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id]);

  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user) {
    throw new Exception('User not found');
  }

  $user['role'] = getRoleName($pdo, $user['role_id']);
  unset($user['role_id']);
  return (isset($customUserID) ? $user :  json_encode($user));
}

try {
  if (!$isGetMethod && !isset($customUserID)) {
    throw new Exception('Invalid request method');
  }
  if (isset($customUserID)) {
    global $userData;
    $userData = getUser($customUserID);
  } else {
    echo getUser($_GET['id']);
  }
} catch (Exception $e) {
  writeLog($e);
  http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()]);
}
