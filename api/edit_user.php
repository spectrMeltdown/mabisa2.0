<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';
require_once '../api/audit_log.php';
require_once 'update_perms.php';
require_once 'get_all_perm_cols.php';
$log = new Audit_log($pdo);

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

  $pdo->beginTransaction();
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
    // get all barangays with all indicators, with its permissions id, or null
    $sql = 'SELECT
  rb.brgyid AS brgyid,
  i.keyctr AS indid,
  urb.permission_id AS permid
FROM refbarangay rb
CROSS JOIN maintenance_area_indicators i
LEFT JOIN user_roles_barangay urb
  ON urb.barangay_id = rb.brgyid
  AND urb.indicator_id = i.keyctr
  AND urb.user_id = :id;
';
    // all barangays, indicators, with perms id or null
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $urbPerms = $stmt->fetchAll();

    // some logging
    // writeLog('BAR PERM IDs WAS');
    // writeLog($urbPerms);
    // writeLog('BAR PERMS WAS');
    // writeLog($barPerms);

    // update bar perms
    updateMultiBarPerms($pdo, $urbPerms, array_keys($barPerms), $allPerms, (int)$id);
  }

  // get the gen perms id of current user
  if (!empty($genPerms)) {
    writeLog('GEN PERMS WAS');
    writeLog($genPerms);
    // each user should only have one global/general perms
    $sql = 'SELECT permissions_id AS genID FROM user_roles WHERE user_id = :id LIMIT 1';

    // actual fetch
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    writeLog('GEN PERM ID WAS');
    writeLog($result);

    // update gen perms
    $genPermsID = updatePerms($pdo, isset($result['genID']) ? $result['genID'] : null, array_keys(array_filter($genPerms, function ($perm) {
      return $perm != 'selectAllGen';
    }, ARRAY_FILTER_USE_KEY)), $allPerms, true);
    if (!empty($genPermsID)) {
      writeLog('SQL params: ');
      writeLog([
        ':user_id' => $id,
        ':permissions_id' => $genPermsID
      ]);
      // insert the new entry into user_roles
      $sql = 'INSERT INTO user_roles(user_id, permissions_id) VALUES (:user_id,  :permissions_id)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':user_id' => $id,
        ':permissions_id' => $genPermsID
      ]);
    }
  }

  $pdo->commit();
  //logging
  $log->userLog('Edited a User with ID: ' . $id . 'to Username: ' . $username . ', Fullname: ' . $fullName . ', Email: ' . $email . ', and Mobile Num: ' . $mobileNum);
} catch (\Throwable $th) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage(), JSON_PRETTY_PRINT);
}

function updateMultiBarPerms(\PDO $pdo, array $urbPerms, array $newPerms, array $allPerms, int $userID)
{
  writeLog('update bar perms');
  // create a new array where permissions are groups per indicator, and indicators are grouped per barangay
  /** @var array */
  $compiledNewPerms = [];
  foreach ($newPerms as $newPermsEntry) {
    writeLog($newPermsEntry);
    // separate string to variables
    $entry = explode('--', $newPermsEntry);
    $barangayID = $entry[0];
    $indicatorID = $entry[1];
    $permissionCol = $entry[2];

    // add permission column
    $compiledNewPerms[$barangayID][$indicatorID][] = $permissionCol;
  }

  writeLog($compiledNewPerms);
  $allPerms = getPermTableNames($pdo);

  // loop barangays
  foreach ($compiledNewPerms as $barangayID => $indicators) {
    // loop indicators per barangay
    foreach ($indicators as $indicatorID => $permissions) {
      // writeLog('urb result is: ');
      // writeLog($urbRes);
      foreach ($urbPerms as $entry) {
        if ($entry['brgyid'] == $barangayID && $entry['indid'] == $indicatorID) {
          writeLog('Updating: ' . $barangayID . ' ' . $indicatorID);
          $permID = updatePerms($pdo, $entry['permid'], array_map(fn($value) => 'assessment_' . $value, $permissions), $allPerms, true);
          if (!empty($permID)) {
            writeLog('SQL params: ');
            writeLog([
              ':user_id' => $userID,
              ':barangay_id' => $entry['brgyid'],
              ':indicator_id' => $entry['indid'],
              ':permission_id' => $permID
            ]);
            // insert the new entry into user_roles_barangay
            $sql = 'INSERT INTO user_roles_barangay(user_id, barangay_id, indicator_id, permission_id) VALUES (:user_id, :barangay_id, :indicator_id, :permission_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
              ':user_id' => $userID,
              ':barangay_id' => $entry['brgyid'],
              ':indicator_id' => $entry['indid'],
              ':permission_id' => $permID
            ]);
          }
        }
      }
    }
  }
}
