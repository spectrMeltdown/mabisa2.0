<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') throw new Exception('Invalid request.');
  if (empty($_GET['type'])) throw new Exception('No selection.');
  global $pdo;

  /** @var string */
  $type = $_GET['type'];

  // get perms
  $sql = "describe permissions;";
  $query = $pdo->query($sql);
  if ($query->rowCount() <= 0) throw new Exception('describe permissions didn\'t return anything');
  $perms = $query->fetchAll();

  switch ($type) {
    case $type !== 'gen':
      foreach ($perms as $perm) {
        if (str_contains($col['Field'], 'assessment')) {
          $allPermissions[] = $col['Field'];
        }
      }
      echo json_encode($rolePermissions);
      break;
    case $type !== 'bar':
      while ($col = $query->fetch(PDO::FETCH_ASSOC)) {
        if (str_contains($col['Field'], 'assessment')) {
          $allPermissions[] = $col['Field'];
        }
      }
      echo json_encode($rolePermissions);
      break;
    default:
      // cancel if incorrect selection made
      throw new Exception('Invalid selection');
  }
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
