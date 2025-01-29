<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';

function getAllUsers(): array
{
  global $pdo;

  $sql = "select * from users";
  $stmt = $pdo->query($sql);
  /** @var User[] */
  $users = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $barangay = isset($row['barangay']) ? $row['barangay'] : 'N/A';
    $role = isset($row['role']) ? $row['role'] : '--';

    $users[] = [
      (string)$row['id'],
      $row['username'],
      $row['full_name'],
      $barangay,
      $row['email'],
      (int)$row['mobile_num'],
      $row['password'],
      $role
    ];
  }

  return $users;
}

echo json_encode(getAllUsers());
