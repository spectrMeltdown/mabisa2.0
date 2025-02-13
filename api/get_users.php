<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';
require_once 'get_role_name.php';

function getAllUsers(): array
{
  global $pdo;

  $sql = "select * from users";
  $stmt = $pdo->query($sql);
  $users = [];
  while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $roleName = empty($user['role_id']) ? '--' : getRoleName($pdo, $user['role_id']);
    $user['role'] = $roleName;
    unset($user['role_id']);
    $users[] = $user;
  }
  return $users;
}

echo json_encode(getAllUsers());
