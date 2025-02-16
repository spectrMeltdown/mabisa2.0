<?php

// TODO UPDATE THIS

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';
require_once 'has_permissions.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  // redirect if logged out
  if (empty($_COOKIE['id'])) {
    header('location: ' . $_SERVER['HTTP_HOST'] . '/Admin/logged_out');
    exit;
  }
  // redirect if not granted permission
  // if (hasPermission()) {
  // }

  /** @var string */
  $roleName = trim($_POST['role_name']);
  // writeLog('role name from post');
  // writeLog($roleName);
  /** @var bool */
  // writeLog('allow bar from post');
  // writeLog(gettype($_POST['allow_bar']));
  // tried filter_var, but doesn't work. (bool) also doesn't work. We just have to convert it manually
  writeLog('allow barangay from post');
  writeLog($_POST['allow_bar']);
  $allowBar = empty($_POST['allow_bar']) ? null : (strtolower(trim($_POST['allow_bar'])) == 'false' ? false : (strtolower(trim($_POST['allow_bar'])) == 'true' ? true : null));
  writeLog('allow barangay after assignment');
  writeLog($allowBar);
  /** @var array */
  $genPerms = json_decode($_POST['genPerms']);
  // writeLog('genPerms from post');
  // writeLog($genPerms);
  /** @var array */
  $barPerms = json_decode($_POST['barPerms']);
  // writeLog('barPerms from post');
  // writeLog($barPerms);

  // cancel if any empty
  if (empty($roleName)) {
    // writeLog('roleName was empty!');
    throw new Exception('Role name was empty!');
  } else if (!isset($allowBar)) {
    // writeLog('allowBarangay was empty!');
    throw new Exception('allowBarangay was empty!');
  } else if (empty($barPerms) && $allowBar) {
    // writeLog('permissions was empty!');
    throw new Exception('no bar perms was given, but allow barangay is true');
  }

  // insert permissions first to get the id 
  global $pdo;
  $barSQL = createSQL($barPerms);
  $genSQL = createSQL($genPerms);


  // writeLog('SQL was: ' . $sql);
  $barPerms = array_combine(
    array_map(fn($key) => ':' . $key, $barPerms), // append colon to each item
    array_fill(0, count($barPerms), true) // set true for each item
  );
  $genPerms = array_combine(
    array_map(fn($key) => ':' . $key, $genPerms), // append colon to each item
    array_fill(0, count($genPerms), true) // set true for each item
  );

  writeLog('insert statement on permissions was: ');
  writeLog($barSQL);
  writeLog($genSQL);
  writeLog('insert params was permissions was: ');
  writeLog($barPerms);
  writeLog($genPerms);

  // execute the sql statements
  $stmt = $pdo->prepare($barSQL);
  $stmt->execute($barPerms);
  /** @var int */
  $barPermID = (int)$pdo->lastInsertId();

  $stmt = $pdo->prepare($genSQL);
  $stmt->execute($genPerms);
  /** @var int */
  $genPermID = (int)$pdo->lastInsertId();

  writeLog('new permission ids were: ');
  writeLog('Gen: ' . (string)$genPermID . ' Bar: ' . (string)$barPermID);

  // finally, insert the role
  $sql = 'insert into roles(name, allow_bar, bar_perms, gen_perms) values (:name, :allow_bar, :bar_perms, :gen_perms)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':name' => $roleName,
    ':allow_bar' => (bool)$allowBar,
    ':bar_perms' => $barPermID,
    ':gen_perms' => $genPermID,
  ]);
  // blank means everything went well
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}

function createSQL(array $permissions): string
{
  $sql = 'insert into permissions(';
  foreach ($permissions as $permission) {
    writeLog($permission);
    $sql .= ' ' . $permission . ',';
  }
  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ') values(';
  foreach ($permissions as $permission) {
    $sql .= ' :' . $permission . ',';
  }
  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ');';

  // remove all newlines (if any)
  return str_replace('\n', '', $sql);
}
