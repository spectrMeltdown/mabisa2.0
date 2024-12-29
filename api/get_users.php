<?php

declare(strict_types=1);
header('Content-Type: application/json');
require_once('../db/db.php');
try {
  // Query to fetch all users
  $sql = "SELECT * FROM users ORDER BY created_at DESC";
  $stmt = $pdo->query($sql);

  $users = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[] = [
      'id' => $row['id'],
      'username' => $row['username'],
      'full_name' => $row['full_name'],
      'barangay' => $row['barangay'],
      'email' => $row['email'],
      'mobile_num' => (int)$row['mobile_num'],
      'policy_read' => (bool)$row['policy_read'],
      'role' => $row['role'],
      'created_at' => $row['created_at'],
    ];
  }

  // Return JSON response
  echo json_encode($users);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to fetch users.', 'details' => $e->getMessage()]);
}
