<?php

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
    $permissionsId = $_POST['permissions_id'];
    /** @var string */
    $roleName = trim($_POST['role_name']);
    /** @var bool */
    $allowBarangay = $_POST['allow_barangay'];
    // writeLog($allowBarangay);
    // writeLog('Type is: ' . gettype($allowBarangay));
    /** @var array */
    $permissions = json_decode($_POST['permissions']);
    // writeLog('permissions is --');
    // writeLog($permissions);

    // cancel if any empty
    if (empty($roleName)) {
      writeLog('roleName was empty!');
      throw new Exception('Role name was empty!');
    } else if (empty($allowBarangay)) {
      writeLog('allowBarangay was empty!');
      throw new Exception('allowBarangay was empty!');
    } else if (empty($permissions)) {
      writeLog('permissions was empty!');
      throw new Exception('Permissions was empty!');
    } else if (empty($id)) {
      writeLog('id was empty!');
      throw new Exception('id was empty!');
    }

    // insert permissions first to get the id 
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
    $sql = 'update roles set name = :name, allow_barangay = :allow_barangay where id = :id;';
    $stmt = $pdo->prepare($sql);
    // writeLog('allow barangay: ');
    // writeLog($allowBarangay);
    $stmt->execute([
      ':id' => $id,
      ':name' => $roleName,
      ':allow_barangay' => (bool)$allowBarangay,
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
