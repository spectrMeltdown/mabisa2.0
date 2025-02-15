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

  // get the general permissions
  $sql = 'select * from permissions where id = :id limit 1';
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $result['role']['gen_perms']]);
  $result['gen_perms'] = $query->fetch(PDO::FETCH_ASSOC);

  // get the barangay permissions
  $sql = 'select * from permissions where id = :id limit 1';
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $result['role']['bar_perms']]);
  $result['bar_perms'] = $query->fetch(PDO::FETCH_ASSOC);

  // remove ids and last_modified
  unset($result['role']['id'], $result['role']['last_modified'], $result['bar_perms']['id'], $result['bar_perms']['last_modified'], $result['gen_perms']['id'], $result['gen_perms']['last_modified']);

  // change the names
  $result['barPerms'] = $result['bar_perms'];
  $result['genPerms'] = $result['gen_perms'];

  // writeLog('Result was: ');
  // writeLog($result);
  echo json_encode($result);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
