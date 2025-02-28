<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';
require_once '../api/audit_log.php';
$log = new Audit_log($pdo);

try {
  if (!$_SERVER['REQUEST_METHOD'] == 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['id'])) throw new Exception('id not found.');

  // get pdo
  global $pdo;

  // assign id
  $id  = trim($_POST['id']);
  $barPermID  = empty($_POST['barPerms']) ? null : $_POST['barPerms'];
  $genPermID  = empty($_POST['genPerms']) ? null : $_POST['genPerms'];

  $pdo->beginTransaction();

  // delete role
  $sql = 'delete from roles where id = :id';
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $id]);

  // delete gen permissions
  if ($genPermID) {
    $sql = 'delete from permissions where id = :gen_perms';
    $query = $pdo->prepare($sql);
    $query->execute([':gen_perms' => $genPermID]);
  }

  // delete bar permissions
  if ($barPermID) {
    $sql = 'delete from permissions where id = :bar_perms';
    $query = $pdo->prepare($sql);
    $query->execute([':bar_perms' => $barPermID]);
  }
  $pdo->commit();

  //logging
  $log->userLog('Deleted a Role with ID: ' . $id);
  echo json_encode('Success', JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage(), JSON_PRETTY_PRINT);
}
