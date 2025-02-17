<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
if (isset($useAsImport)) require_once 'logging.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['role_id'])) throw new Exception('Invalid id.');
  if ($_POST['role_id'] == 'noSelection') die;
  global $pdo;

  /** @var int */
  $id = $_POST['role_id'];
  /** @var bool|string */
  $truePerms = empty($_POST['cleanPerms']) ? true : $_POST['cleanPerms'];
  /** @var bool|string */
  $permsOnly = empty($_POST['permsOnly']) ? true : $_POST['permsOnly'];

  // get bar permissions
  $query = $pdo->prepare("select * from permissions inner join roles on roles.bar_perms = permissions.id where roles.id = :id");
  $query->execute([
    'id' => $id
  ]);
  // will return false if result is empty
  $barPerms = $query->fetch(PDO::FETCH_ASSOC);
  $barPerms = $barPerms ? $barPerms : []; // replace with empty array if false

  // get gen permissions
  $query = $pdo->prepare("select * from permissions inner join roles on roles.gen_perms = permissions.id where roles.id = :id");
  $query->execute([
    'id' => $id
  ]);
  // will return false if result is empty
  $genPerms = $query->fetch(PDO::FETCH_ASSOC);
  $genPerms = $genPerms ? $genPerms : []; // replace with empty array if false

  if ($permsOnly) {
    if ($barPerms) {
      // remove id and last_modified cols
      array_pop($barPerms);
      array_shift($barPerms);
    }
    if ($genPerms) {
      // remove id and last_modified cols
      array_pop($genPerms);
      array_shift($genPerms);
    }
  }

  //get all items where value is true (or 1 in this case)
  if ($truePerms) {
    if ($barPerms) {
      $barPerms = array_filter($barPerms, function ($perm) {
        // writeLog('Permission is:');
        // writeLog($permission);
        return $perm == 1;
      });
    }
    if ($genPerms) {
      $genPerms = array_filter($genPerms, function ($perm) {
        // writeLog('Permission is:');
        // writeLog($permission);
        return $perm == 1;
      });
    }
  }

  if (isset($useAsImport)) echo json_encode([
    'barPerms' => $barPerms,
    'genPerms' => $genPerms,
  ], JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
