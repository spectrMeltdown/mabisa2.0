<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['id'])) throw new Exception('Invalid id.');
  global $pdo;

  /** @var int */
  $id = $_POST['id'];

  $query = $pdo->prepare("select * from user_roles where id = :id");
  $query->execute([
    'id' => $id
  ]);

  $userRoles = $query->fetch(PDO::FETCH_ASSOC);

  // remove first item (id)
  array_shift($userRoles);
  // remove last item (last modified)
  array_pop($userRoles);

  //get all items where value is true (or 1 in this case)
  $userRoles = array_filter($userRoles, function ($permission) {
    writeLog('Permission is:');
    writeLog($permission);
    return $permission == 1;
  });

  echo json_encode($userRoles, JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
