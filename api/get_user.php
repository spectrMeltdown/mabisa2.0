<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
require_once $pathPrepend . 'models/user_model.php';
require_once $pathPrepend . 'models/role_model.php';
require_once $pathPrepend . 'db/db.php';

$isGetMethod = $_SERVER['REQUEST_METHOD'] === 'GET';

function getUser(string $id)
{
  global $pdo;
  global $isGetMethod;
  global $customUserID;

  if (!isset($_GET['id']) && !$isGetMethod) {
    throw new Exception('No id provided!');
  }

  if ($id === 'self' && isset($_COOKIE['id'])) {
    // echo "was a cookie id!";
    $id = $_COOKIE['id'];
  }

  $sql = "SELECT * FROM user_policy WHERE id=:id LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id]);

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    throw new Exception('User not found');
  }

  // Build the response
  $response = [
    'id' => (string)$row['id'],
    'username' => $row['username'] ?? '',
    'fullName' => $row['fullName'] ?? '',
    'barangay' => $row['barangay'] ?? 'N/A',
    'email' => $row['email'] ?? '',
    'mobileNo' => (int)($row['mobileNo'] ?? 0),
    'policyRead' => (bool)($row['policyRead'] ?? false),
    'role' => isset($row['accessLevel']) ? getRole($row['accessLevel'])?->toString() : '--'
  ];

  if (isset($customUserID)) {
    return $response;
  }
  return json_encode($response);
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
  http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()]);
}
