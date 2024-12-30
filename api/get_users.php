<?php

declare(strict_types=1);
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
    $barangay = isset($row['barangay']) ? getBarangay($row['barangay']) : 'N/A';
    $role = isset($row['accessLevel']) ? getRole($row['accessLevel'])?->toString() : '--';

    $users[] = new User(
      (string)$row['user_idno'],
      $row['username'],
      $row['full_name'],
      $barangay,
      $row['email_add'],
      (int)$row['mobile_no'],
      $row['passwrd'],
      (bool)$row['policyRead'],
      $role
    );
  }

  return $users;
}