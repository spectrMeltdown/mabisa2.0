<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
require_once '../models/user_model.php';
require_once '../models/role_model.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new Exception('Invalid request method');
  }

  if (!isset($_GET['id'])) {
    throw new Exception('No id provided!');
  }

  // Example query
  $id = $_GET['id'];
  $sql = "SELECT * FROM user_policy WHERE id=:id";
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
    'password' => $row['password'] ?? '',
    'policyRead' => (bool)($row['policyRead'] ?? false),
    'role' => isset($row['accessLevel']) ? getRole($row['accessLevel'])?->toString() : '--'
  ];

  echo json_encode($response);
} catch (Exception $e) {
  http_response_code(500); // Set HTTP 500 response code
  echo json_encode(['error' => $e->getMessage()]);
}
