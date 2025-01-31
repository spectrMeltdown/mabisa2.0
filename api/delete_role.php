<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';

try {
  if (!$_SERVER['REQUEST_METHOD'] == 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['id'])) throw new Exception('id not found.');
  if (empty($_POST['permissions_id'])) throw new Exception('permissions id not found.');

  // get pdo
  global $pdo;

  // assign id
  $id  = trim($_POST['id']);
  $permissionsID  = trim($_POST['permissions_id']);

  // delete permissions (foreign key constraint on role)
  $sql = 'delete from permissions where id = :permissions_id';
  $query = $pdo->prepare($sql);
  $query->execute([':permissions_id' => $permissionsID]);

  // delete role
  $sql = 'delete from roles where id = :id';
  $query = $pdo->prepare($sql);
  $query->execute([':id' => $id]);

  echo json_encode('Success');
} catch (\Throwable $th) {
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage());
}
