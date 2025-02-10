<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['id'])) throw new Exception('Invalid id.');
  global $pdo;

  // result var
  /** @var array */
  $result = [];

  // get role
  $sql = "select * from roles where id = :id limit 1";
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $_POST['id']]);
  $result['role'] = $query->fetch(PDO::FETCH_ASSOC);

  // writeLog('Result with role was: ');
  // writeLog($result);

  $sql = 'select * from permissions where id = :id limit 1';
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $result['role']['permissions_id']]);
  $result['permissions'] = $query->fetch(PDO::FETCH_ASSOC);

  // remove id and last_modified
  unset($result['role']['id'], $result['role']['last_modified'], $result['permissions']['id'], $result['permissions']['last_modified']);

  writeLog('Result was: ');
  writeLog($result);
  echo json_encode($result);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
