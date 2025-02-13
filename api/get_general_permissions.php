<?php

declare(strict_types=1);
$useAsImport; // for get_permissions.php
require 'logging.php';
require_once '../db/db.php';
require_once 'get_permissions.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['role_id'])) throw new Exception('Invalid ID.');
  $result = [];

  writeLog('IN GENERAL PERMS');

  /** @var int */
  $roleID = (int)$_POST['role_id'];

  writeLog('role id: ');
  writeLog($roleID);

  // get the role
  $sql = "select * from roles where id = :role_id limit 1";
  $query = $pdo->prepare($sql);
  $query->execute([':role_id' => $roleID]);
  $role = $query->fetch(PDO::FETCH_ASSOC);

  writeLog('the role was: ');
  writeLog($role);

  // super admins are auto granted all permissions
  if ($role['name'] == 'Super Admin') {
    echo json_encode('Super Admin');
    exit;
  }

  $hasBarangay = $role['allow_barangay'];
  writeLog('has barangay: ');
  writeLog($hasBarangay);

  // get permissions of the role
  global $rolePermissions;
  writeLog('original role perms were:');
  writeLog($rolePermissions);

  // remove allow barangay from the perms
  $rolePermissions = array_filter($rolePermissions, fn($perm) => !str_contains($perm, 'allow') && !str_contains($perm, 'barangay'), ARRAY_FILTER_USE_KEY);

  writeLog('after filter: ');
  writeLog($rolePermissions);

  // check if role allows barangays
  if ($hasBarangay == 1) {
    writeLog('has barangay!');
    // TODO: allow this later on, because then the user can access all barangays if so
    // remove all permissions that start with the name 'assessment'
    $rolePermissions = array_filter($rolePermissions, function ($permission) {
      writeLog($permission);
      return !str_contains((string)$permission, 'assessment');
    }, ARRAY_FILTER_USE_KEY);
  }

  writeLog('Final result: ');
  writeLog($rolePermissions);
  echo json_encode($rolePermissions);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
