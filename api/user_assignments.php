<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
require_once '../db/db.php';

try {
  global $pdo;

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid method.');
  }

  $sql;
  $params = [];
  if (!empty($_POST['id'])) {
    $sql = 'select id, name, auditorID from refbarangay inner join user_policy on refbarangay.auditor == user_policy.id where user_policy.id = :id';
    if ($_POST['id'] === 'self') {
    } else {
      $id = $_POST['id'];
    }
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

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
} catch (Exception $e) {
  http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()]);
}
