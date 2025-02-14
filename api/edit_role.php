<?php

// TODO UPDATE THIS

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    /** @var int */
    $id = $_POST['id'];
    /** @var int */
    $barPermsID = $_POST['barPermsID'];
    writeLog('bar perms id was: ');
    writeLog($barPermsID);
    /** @var int */
    $genPermsID = $_POST['genPermsID'];
    writeLog('gen perms id was: ');
    writeLog($genPermsID);
    /** @var string */
    $roleName = trim($_POST['role_name']);
    /** @var bool */
    $allowBar = $_POST['allow_bar'];
    // writeLog($allowBar);
    // writeLog('Type is: ' . gettype($allowBar));
    /** @var array */
    $genPerms = json_decode($_POST['genPerms']);
    $barPerms = json_decode($_POST['barPerms']);
    writeLog('gen perms is');
    writeLog($genPerms);
    writeLog('bar perms is');
    writeLog($barPerms);
    exit;

    // cancel if any empty
    if (empty($roleName)) {
      writeLog('roleName was empty!');
      throw new Exception('Role name was empty!');
    } else if (empty($allowBar)) {
      writeLog('allowBarangay was empty!');
      throw new Exception('allowBarangay was empty!');
    } else if (empty($barPerms) && $allowBar) {
      // writeLog('permissions was empty!');
      throw new Exception('no bar perms was given, but allow barangay is true');
    } else if (empty($id)) {
      writeLog('id was empty!');
      throw new Exception('id was empty!');
    }

    // update permissions first
    global $pdo;
    $sql = 'update permissions set';
    foreach ($permissions as $key => $value) {
      $sql .= ' ' . $key . ' = ';
      $sql .= ' ' . ($value == 1 ? 'true' : 'false') . ',';
    }
    // remove trailing comma
    $sql = rtrim($sql, ',');
    $sql .= ' where id = :permissions_id;';

    // remove all newlines (if any)
    str_replace('\n', '', $sql);

    // writeLog('Permissions update');
    // writeLog($sql);

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':permissions_id' => $permissionsId]);

    // finally, insert the role
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
  } catch (\Throwable $th) {
    writeLog($th);
    echo json_encode($th->getMessage());
    exit;
  }
}
