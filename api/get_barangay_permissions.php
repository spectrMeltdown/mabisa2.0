<?php

declare(strict_types=1);
$useAsImport; // for get_permissions.php
require 'logging.php';
require_once '../db/db.php';
require_once 'get_permissions.php'; // gets the role permissions

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['role_id'])) throw new Exception('Invalid ID.');
  if ($_POST['role_id'] == '') die;
  $result = [];

  // get all barangays
  $sql = "SELECT brgyid, name FROM refbarangay";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
  writeLog('Barangays was:');
  writeLog($barangays);

  // get all permissions
  $sql = "SELECT * FROM permissions";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $allPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  writeLog('all permissions was:');
  writeLog($allPermissions);

  // Fetch taken permissions in user_roles (criteria)
  // $sql = "SELECT user_roles_barangay_id, permission_id FROM user_roles";
  // $stmt = $pdo->prepare($sql);
  // $stmt->execute();
  // $takenCriteria = [];
  // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  //   $takenCriteria[$row['brgyid']][] = $row['permission_id'];
  // }

  // Fetch all criteria instead (using active version)
  $sql = "SELECT user_roles_barangay_id, permission_id FROM user_roles";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $takenCriteria = [];
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $takenCriteria[$row['brgyid']][] = $row['permission_id'];
  }

  // Fetch taken permissions in user_roles_barangay (assessment)
  $sql = "SELECT user_roles_barangay_id, permission_id FROM user_roles_barangay";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $takenAssessment = [];
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $takenAssessment[$row['barangay_id']][] = $row['permission_id'];
  }
  writeLog('taken assessment permissions was:');
  writeLog($takenAssessment);

  // Build the JSON response
  $response = [];
  foreach ($barangays as $barangay) {
    $barangayId = $barangay['brgyid'];

    // // Get available criteria permissions (exclude taken ones)
    // $availableCriteria = array_diff(array_keys($permissionsMap), $takenCriteria[$barangayId] ?? []);

    // Get available assessment permissions (exclude taken ones)
    $availableAssessment = array_diff(array_keys($permissionsMap), $takenAssessment[$barangayId] ?? []);

    $response[] = [
      "Barangay" => $barangay['name'],
      "Criteria" => array_map(fn($id) => $permissionsMap[$id], $availableCriteria),
      "Permissions" => array_map(fn($id) => $permissionsMap[$id], $availableAssessment)
    ];
  }

  // Return JSON response
  echo json_encode($response, JSON_PRETTY_PRINT);

  // // get all permissions columns
  // $sql = "describe permissions;";
  // $query = $pdo->query($sql);
  // writeLog($query);

  // // add all permissions to the column
  // if ($query->rowCount() <= 0) throw new Exception('describe permissions didn\'t return anything');
  // while ($col = $query->fetch(PDO::FETCH_ASSOC)) {
  //   $result['permissions'][] = $col;
  // }
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message);
}
