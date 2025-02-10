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
  /** @var bool */
  $isSuper = isset($details['is_super_admin']) ? true : false;


  // PERMISSIONS DETAILS
  $barPerms = isset($_POST['barPerms']) ? array_keys($_POST['barPerms']) : null;
  $genPerms = isset($_POST['genPerms']) ? array_keys($_POST['genPerms']) : null;
  global $pdo;

  writeLog('Gen perms');
  writeLog($genPerms);
  writeLog('Bar perms');
  writeLog($barPerms);
  // die;

  // create user first to get user id needed for later
  $sql = 'insert into users (username, full_name, email, mobile_num, password, role_id) values (:username, :full_name, :email, :mobile_num, :password, :role_id)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':username' => $username,
    ':full_name' => $fullName,
    ':email' => $email,
    ':mobile_num' => substr($mobileNum, 2),
    ':password' => password_hash($pass, PASSWORD_BCRYPT),
    ':role_id' => (int)$role_id,
  ]);
  /** @var int */
  $newUserID = $pdo->lastInsertId();

  // update perms to set user_roles_barangay_id
  $newUserRolesBarangayPerms = null;
  if (isset($barPerms) && $barPerms != null) {
    writeLog('inserting bar perms');
    // create a new array where permissions are groups per indicator, and indicators are grouped per barangay
    /** @var array */
    $compiledPerms = [];
    foreach ($barPerms as $barPermsEntry) {
      // separate string to variables
      $entry = explode('--', $barPermsEntry);
      $barangayID = $entry[0];
      $indicatorID = $entry[1];
      $permissionCol = $entry[2];


      if (!isset($compiledPerms[$barangayID][$indicatorID])) {
        $compiledPerms[$barangayID][$indicatorID] = [];
      }

      // add permission column
      $compiledPerms[$barangayID][$indicatorID][] = $permissionCol;
    }
    // writeLog($compiledPerms);
    // exit;
    foreach ($compiledPerms as $barangayID => $indicators) {
      foreach ($indicators as $indicatorID => $permissions) {
        $newPermissionID = insertPermissions($pdo, $permissions, true);
        $sql = 'insert into user_roles_barangay (user_id, barangay_id, indicator_id, permission_id) values (:user_id, :barangay_id, :indicator_id, :permission_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          ':user_id' => $newUserID,
          ':barangay_id' => $barangayID,
          ':indicator_id' => $indicatorID,
          ':permission_id' => $newPermissionID,
        ]);
        $newUserRolesBarangayPerms = $pdo->lastInsertId();
      }
    }
  }

  // update user_roles
  if (isset($genPerms) && $genPerms != null) {
    writeLog('inserting gen perms');
    // insert permissions
    $newPermissionID = insertPermissions($pdo, $genPerms);
    $sql = 'insert into user_roles (user_id, permissions_id, user_roles_barangay_id) values (:user_id, :permissions_id, :user_roles_barangay_id)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':user_id' => $newUserID,
      ':permissions_id' => $newPermissionID,
      ':user_roles_barangay_id' => $newUserRolesBarangayPerms, // default is null, if not granted
    ]);
  } else if ($isSuper) {
    // // get all the possible permissions
    // $sql = "describe permissions;";
    // $query = $pdo->query($sql);
    // $allPermissions = [];
    // // add all permissions to the column
    // if ($query->rowCount() <= 0) throw new Exception('describe permissions didn\'t return anything');
    // while ($col = $query->fetch(PDO::FETCH_ASSOC)) {
    //   // writeLog('Col is: ');
    //   // writeLog($col);
    //   // add if assessment word is in it
    //   if (str_contains($col['Field'], 'assessment')) {
    //     $allPermissions[] = $col['Field'];
    //   }
    // }
  }
  // writeLog('Execution permissions was: ');
  // writeLog($permissions);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($th);
  echo json_encode($message);
}

// expects a column names $permissions
// returns the id of the new permission row
function insertPermissions($pdo, $permissions, $appendAssessment = false): int
{
  global $pdo;
  $sql = 'insert into permissions(';
  foreach ($permissions as $permission) {
    $sql .= ' ' . ($appendAssessment ? 'assessment_' : '') . $permission . ',';
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
