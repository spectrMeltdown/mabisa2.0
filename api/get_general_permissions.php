<?php

declare(strict_types=1);
$useAsImport; // for get_permissions.php
$disableLogging = true;
require_once 'logging.php';
require_once '../db/db.php';
require_once 'get_role_permissions.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['role_id'])) throw new Exception('Invalid ID.');
  $result = [];

  writeLog('IN GENERAL PERMS');

  /** @var int */
  $roleID = (int)$_POST['role_id'];
  /** @var int */
  $userID = empty($_POST['id']) ? null : (int)$_POST['id'];

  writeLog('role id: ');
  writeLog($roleID);
  writeLog('user id: ');
  writeLog($userID);

  // get the role
  $sql = "select name from roles where id = :role_id limit 1";
  $query = $pdo->prepare($sql);
  $query->execute([':role_id' => $roleID]);
  $role = $query->fetch(PDO::FETCH_ASSOC);

  writeLog('the role was: ');
  writeLog($role);

  // super admins are auto granted all permissions
  if (strtolower($role['name']) == 'super admin') {
    echo json_encode('Super Admin', JSON_PRETTY_PRINT);
    exit;
  }

  $response = [];

  // get general permissions

  global $genPerms;
  $roleGenPerms = $genPerms;
  writeLog('original gen perms were:');
  writeLog($roleGenPerms);

  // remove allow barangay from the perms
  $roleGenPerms = array_filter($roleGenPerms, fn($value, $perm) => !str_contains($perm, 'allow') && !str_contains($perm, 'bar') && $value == 1, ARRAY_FILTER_USE_BOTH);
  // writeLog('after filter: ');
  // writeLog($roleGenPerms);
  $response['available'] = array_keys($roleGenPerms);
  if ($userID != null) {
    $sql = 'SELECT * FROM permissions p JOIN user_roles ur ON ur.permissions_id = p.id WHERE ur.user_id = :id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $userID]);
    $permissions = $stmt->fetch(PDO::FETCH_ASSOC);
    writeLog('user taken perms are');
    writeLog($permissions);
    if ($permissions != false) {
      writeLog('perms not false!!');
      $permissions = array_filter($permissions, fn($value) => $value == 1);
      $response['taken'] = array_keys($permissions);
    }
  }

  writeLog('Final result: ');
  writeLog($response);
  echo json_encode($response, JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage(), JSON_PRETTY_PRINT);
}
