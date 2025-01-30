<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';
require_once 'get_role_name.php';

try {
  global $pdo;
  $query = "select * from roles";
  $query = $pdo->query($query);
  $users = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($users);
} catch (\Throwable $th) {
  http_response_code(500);
  echo json_encode(['error' => (string)$th]);
}
