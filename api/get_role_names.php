<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';

try {
  global $pdo;
  $sql = "select name from roles";
  $query = $pdo->query($sql);
  $roleNames = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($roleNames);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
