<?php

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
  writeLog('role name from post');
  writeLog($roleName);
  /** @var bool */
  $allowBarangay = $_POST['allow_barangay'];
  writeLog('allow barangay from post');
  writeLog($allowBarangay);
  /** @var array */
  $permissions = json_decode($_POST['permissions']);
  writeLog('permissions from post');
  writeLog($permissions);

  // cancel if any empty
  if (empty($roleName)) {
    // writeLog('roleName was empty!');
    throw new Exception('Role name was empty!');
  } else if (empty($allowBarangay)) {
    // writeLog('allowBarangay was empty!');
    throw new Exception('allowBarangay was empty!');
  } else if (empty($permissions)) {
    // writeLog('permissions was empty!');
    throw new Exception('Permissions was empty!');
  }

  // insert permissions first to get the id 
  global $pdo;
  $sql = 'insert into permissions(';
  foreach ($permissions as $permission) {
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
  str_replace('\n', '', $sql);

  // writeLog('SQL was: ' . $sql);
  $permissions = array_combine(
    array_map(fn($key) => ':' . $key, $permissions), // append colon to each item
    array_fill(0, count($permissions), true) // set true for each item
  );

  writeLog('insert statement on permissions was: ');
  writeLog($sql);
  writeLog('insert params was permissions was: ');
  writeLog($permissions);
  $stmt = $pdo->prepare($sql);
  $stmt->execute($permissions);
  /** @var int */
  $permissionID = (int)$pdo->lastInsertId();

  writeLog('new permission id was: ');
  writeLog($permissionID);
  // finally, insert the role
  $sql = 'insert into roles(name, allow_barangay, permissions_id) values (:name, :allow_barangay, :permissions_id)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':name' => $roleName,
    ':allow_barangay' => (bool)$allowBarangay,
    ':permissions_id' => $permissionID,
  ]);
  // blank means everything went well
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
