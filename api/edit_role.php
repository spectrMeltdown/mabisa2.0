<?php

// TODO UPDATE THIS

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';
require 'get_all_perm_cols.php';
require 'update_perms.php';
require_once '../api/audit_log.php';
$log = new Audit_log($pdo);

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  writeLog('IN EDIT ROLE');
  /** @var int */
  $id = $_POST['id'];
  /** @var int */
  $barPermsID = $_POST['barPermID'];
  // writeLog('bar perms id was: ');
  // writeLog($barPermsID);
  /** @var int */
  $genPermsID = $_POST['genPermID'];
  // writeLog('gen perms id was: ');
  // writeLog($genPermsID);
  /** @var string */
  $roleName = trim($_POST['role_name']);
  /** @var bool */
  // refer to create_role.php for more info
  $allowBar = empty($_POST['allow_bar']) ? null : (strtolower(trim($_POST['allow_bar'])) == 'false' ? false : (strtolower(trim($_POST['allow_bar'])) == 'true' ? true : null));
  // writeLog($allowBar);
  // writeLog('Type is: ' . gettype($allowBar));
  /** @var array */
  $genPerms = array_values(json_decode($_POST['genPerms']));
  $barPerms = array_values(json_decode($_POST['barPerms']));
  writeLog('gen perms is');
  writeLog($genPerms);
  writeLog('bar perms is');
  writeLog($barPerms);

  // cancel if any empty
  if (empty($roleName)) {
    writeLog('roleName was empty!');
    throw new Exception('Role name was empty!');
  } else if (!isset($allowBar)) {
    writeLog('allowBarangay was empty!');
    throw new Exception('allowBarangay was empty!');
  } else if (empty($barPerms) && $allowBar) {
    // writeLog('permissions was empty!');
    throw new Exception('no bar perms was given, but allow barangay is true');
  } else if (empty($id)) {
    writeLog('id was empty!');
    throw new Exception('id was empty!');
  }

  global $pdo;
  // get all permissions
  $allPerms = getPermTableNames($pdo);
  // writeLog($allPerms);

  // update global permissions
  updatePerms($pdo, $genPermsID, $genPerms, $allPerms);

  // update bar permissions
  updatePerms($pdo, $barPermsID, $barPerms, $allPerms);

  // update the role
  $sql = 'update roles set name = :name, allow_bar = :allow_bar where id = :id;';
  $stmt = $pdo->prepare($sql);
  // writeLog('allow barangay: ');
  // writeLog($allowBar);
  $stmt->execute([
    ':id' => $id,
    ':name' => $roleName,
    ':allow_bar' => (bool)$allowBar,
  ]);
  // writeLog('Role update');
  // writeLog($sql);
  // blank means everything went well
  $log->userLog('Edited a Role with ID: '.$id.' to Name:'.$roleName);//logging
} catch (\Throwable $th) {
  writeLog($th);
  echo json_encode($th->getMessage(), JSON_PRETTY_PRINT);
}
