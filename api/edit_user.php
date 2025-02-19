<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';
require 'get_all_perm_cols.php';
require 'update_perms.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request method.');
  if (empty($_POST['details']['id'])) throw new Exception('No ID given.');
  // get post vars
  /** @var array */
  $details = empty($_POST['details']) ? [] : $_POST['details'];
  /** @var array */
  $genPerms = empty($_POST['genPerms']) ? [] : $_POST['genPerms'];
  /** @var array */
  $barPerms = empty($_POST['barPerms']) ? [] : $_POST['barPerms'];

  // assign individual variables
  /** @var int|string */
  $id = $details['id'];
  /** @var string */
  $fullName = !empty($details['fullName']) ? trim($details['fullName']) : null;
  /** @var string */
  $username = !empty($details['username']) ? trim($details['username']) : null;
  /** @var string */
  $email = !empty($details['email']) ?  trim($details['email']) : null;
  /** @var string */
  $mobileNum = !empty($details['mobileNum']) ? substr(trim((string)$details['mobileNum']), 3) : null;
  /** @var string */
  $pass = !empty($details['pass']) ? trim($details['pass']) : null;

  // if self was specified, use cookie id
  if ($id == 'self') $id = $_COOKIE['id'];

  // initialize other vars
  global $pdo;
  $shouldExecute = false;
  $sql = 'UPDATE users SET ';
  $params = [':id' => $id];

  // append field(s) to update and set params
  if ($fullName) {
    $sql .= 'full_name = :fullName, ';
    $params[':fullName'] = $fullName;
    $shouldExecute = true;
  }
  if ($username) {
    $sql .= 'username = :username, ';
    $params[':username'] = $username;
    $shouldExecute = true;
  }
  if ($email) {
    $sql .= 'email = :email, ';
    $params[':email'] = $email;
    $shouldExecute = true;
  }
  if ($mobileNum) {
    $sql .= 'mobile_num = :mobileNum, ';
    $params[':mobileNum'] = $mobileNum;
    $shouldExecute = true;
  }
  if ($pass) {
    $sql .= 'password = :pass, ';
    $params[':pass'] = password_hash($pass, PASSWORD_BCRYPT);
    $shouldExecute = true;
  }

  // will execute only if at least on parameter was passed
  if ($shouldExecute) {
    // remove trailing space and comma on sql statement
    $sql = substr($sql, 0, -2);

    // append the id
    $sql .= ' where id = :id;';

    // EXECUTE
    writeLog('statement was:');
    writeLog($sql);
    writeLog('params was:');
    writeLog($params);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  }

  // get all perms
  $allPerms = getPermTableNames($pdo);

  // get the bar perms id of current user (multiple)
  if (!empty($barPerms)) {
    $sql = 'SELECT barangay_id as brgyid, indicator_id as indid, permission_id AS permid FROM user_roles_barangay WHERE user_id = :id';

    // actual fetch
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // some logging
    writeLog('BAR PERM IDs WAS');
    writeLog($result);
    writeLog('BAR PERMS WAS');
    writeLog($barPerms);

    // update bar perms
    updateMultiBarPerms($pdo, $result, $barPerms, $allPerms);
  }

  // get the gen perms id of current user
  if (!empty($genPerms)) {
    // each user should only have one global/general perms
    $sql = 'SELECT permissions_id AS genID FROM users WHERE user_id = :id LIMIT 1';

    // actual fetch
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    writeLog('GEN PERM ID WAS');
    writeLog($result);

    // update gen perms
    updatePerms($pdo, $result[0]['genID'], $genPerms, $allPerms);
  }
} catch (\Throwable $th) {
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage(), JSON_PRETTY_PRINT);
}

function updateMultiBarPerms(\PDO $pdo, array $urbRes, array $newPerms, array $allPerms)
{
  writeLog('update bar perms');
  // create a new array where permissions are groups per indicator, and indicators are grouped per barangay
  /** @var array */
  $compiledNewPerms = [];
  foreach ($newPerms as $newPermsEntry) {
    // separate string to variables
    $entry = explode('--', $newPermsEntry);
    $barangayID = $entry[0];
    $indicatorID = $entry[1];
    $permissionCol = $entry[2];

    if (!isset($compiledPerms[$barangayID][$indicatorID])) {
      $compiledNewPerms[$barangayID][$indicatorID] = [];
    }

    // add permission column
    $compiledNewPerms[$barangayID][$indicatorID][] = $permissionCol;
  }

  writeLog($compiledNewPerms);
  // exit;

  // foreach ($compiledNewPerms as $barangayID => $indicators) {
  //   foreach ($indicators as $indicatorID => $permissions) {
  //     $sql = 'UPDATE permissions SET :permission = 1 WHERE id = :id';
  //     $stmt = $pdo->prepare($sql);
  //     $stmt->execute([
  //       ':user_id' => $newUserID,
  //       ':barangay_id' => $barangayID,
  //       ':indicator_id' => $indicatorID,
  //       ':permission_id' => $newPermissionID,
  //     ]);
  //     $newUserRolesBarangayPerms = $pdo->lastInsertId();
  //   }
  // }
}
