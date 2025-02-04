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

  // get the role
  $sql = "select * from roles where id = :role_id limit 1";
  $query = $pdo->prepare($sql);
  $query->execute([':role_id' => $_POST['role_id']]);
  $role = $query->fetch(PDO::FETCH_ASSOC);

  // super admins are auto granted all permissions
  if ($role['name'] == 'Super Admin') {
    echo json_encode('Super Admin');
    exit;
  }

  $hasBarangay = ['allow_barangay'];
  writeLog('has barangay: ');
  writeLog($hasBarangay);

  // get permissions of the role
  global $rolePermissions;

  // check if role allows barangays
  if ($hasBarangay == true) {
    // remove all permissions that start with the name 'assessment'
    array_filter($rolePermissions, function ($permission) {
      writeLog($permission);
      return strstr((string)$permission, 'assessment');
    });
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
