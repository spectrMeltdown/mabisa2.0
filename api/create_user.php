<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once '../db/db.php';
require_once 'logging.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  // writeLog($_POST);
  // exit;
  if (empty($_POST['details'])) throw new Exception('Details missing.');
  /** @var array */
  $details = $_POST['details'];

  // ACCOUNTS DETAILS
  /** @var string */
  $fullName = trim($details['fullName']);
  /** @var string */
  $username = trim($details['username']);
  /** @var string */
  $email = trim($details['email']);
  /** @var string */
  $mobileNum = trim($details['mobileNum']);
  /** @var string */
  $pass = trim($details['pass']);
  /** @var string */
  $role_id = $details['role'];

  // PERMISSIONS DETAILS
  $barPerms = !empty($_POST['barPerms']) ? array_keys($_POST['barPerms']) : null;
  $genPerms = !empty($_POST['genPerms']) ? array_keys($_POST['genPerms']) : null;
  global $pdo;

  // create user first to get user id needed for later
  $sql = 'insert into users (username, full_name, email, mobile_num, password, role_id) values (:username, :full_name, :email, :mobile_num, :password, :role_id)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':username' => $username,
    ':full_name' => $fullName,
    ':email' => $email,
    ':mobile_num' => substr($mobileNum, 2),
    ':password' => password_hash($pass, PASSWORD_BCRYPT),
    ':role_id' => (int)$role,
  ]);
  /** @var int */
  $userID = $pdo->lastInsertId();

  // update perms to set user_roles_barangay_id
  if (!empty($barPerms)) {
    // create a new array where permissions are groups per indicator, and indicators are grouped per barangay
    /** @var array */
    $compiledPerms = [];
    foreach ($barPerms as $barPermsEntry) {
      // separate string to variables
      $entry = explode('--', $barPermsEntry);
      $barangayID = $entry[0];
      $indicatorID = $entry[1];
      $permissionCol = $entry[2];

      // add to compiled perms
      if (!isset($compiledPerms[$barangayID][$indicatorID])) {
        $compiledPerms[$barangayID][] = $indicatorID;
      }
      $compiledPerms[$barangayID][$indicatorID] = ;
    }
    $permissionID = insertPermissions($pdo, $barPerms);
    $sql = 'insert into user_roles_barangay (user_id, barangay_id, indicator_id, permission_id) values (:username, :full_name, :email, :mobile_num, :password, :role_id)';
    $stmt = $pdo->prepare($sql);
  }
  // insert permissions first to get the id 


  // writeLog('Execution permissions was: ');
  // writeLog($permissions);

  // update user_roles_barangay
  // 
  exit;
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}

// expects a column names $permissions
// returns the id of the new permission row
function insertPermissions($pdo, $permissions): int
{
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

  $stmt = $pdo->prepare($sql);
  $stmt->execute($permissions);
  return (int)$pdo->lastInsertId();
}
