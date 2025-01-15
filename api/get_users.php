<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';
require_once '../models/user_model.php';
require_once '../models/role_model.php';

function getAllUsers(): array
{
  global $pdo;

  $sql = "select * from user_policy";
  $stmt = $pdo->query($sql);
  /** @var User[] */
  $users = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $barangay = isset($row['barangay']) ? $row['barangay'] : 'N/A';
    $role = isset($row['accessLevel']) ? getRole($row['accessLevel'])?->toString() : '--';

    $users[] = new User(
      (string)$row['id'],
      $row['username'],
      $row['fullName'],
      $barangay,
      $row['email'],
      (int)$row['mobileNo'],
      $row['password'],
      (bool)$row['policyRead'],
      $role
    );
  }

  return $users;
}

echo json_encode(getAllUsers());
