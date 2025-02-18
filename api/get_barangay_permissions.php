<?php

declare(strict_types=1);
$useAsImport; // for get_permissions.php
$permsOnly = false;
$disableLogging; // set to true to disable for this file
require 'logging.php';
require_once '../db/db.php';
require 'get_all_perm_cols.php';
require_once 'get_role_permissions.php'; // gets the role permissions

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  if (empty($_POST['role_id'])) throw new Exception('Invalid ID.');
  if ($_POST['role_id'] == '') die;
  $result = [];

  writeLog('IN GET BARANGAY PERMS');

  /** @var int */
  $roleID = (int)$_POST['role_id'];
  /** @var int|null */
  $userID = empty($_POST['id']) ? '' : (int)$_POST['id'];

  // check if role allows barangay
  $sql = "SELECT allow_bar FROM roles where id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $roleID]);
  $allowBarangay = $stmt->fetch(PDO::FETCH_ASSOC)['allow_bar'];

  // writeLog('allow barangay is: ');
  // writeLog($allowBarangay);

  // exit if the role doesn't allow barangay assignments
  if ($allowBarangay != 1) exit;

  // get all barangays
  $sql = "SELECT brgyid, brgyname FROM refbarangay";
  $stmt = $pdo->query($sql);
  $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // writeLog('Barangays was:');
  // writeLog($barangays);

  // get all permissions that start with "assessment"
  $allPermissions = getPermTableNames($pdo, 'assessment');
  writeLog('All permissions was:');
  writeLog($allPermissions);

  // Fetch all indicators from the current active version 
  $sql = "SELECT i.keyctr as id, i.indicator_code as code, i.relevance_def as description
  from maintenance_area_indicators i 
  inner join maintenance_criteria_setup cs
  on cs.indicator_keyctr = i.keyctr 
  inner join maintenance_criteria_version v
  on v.keyctr = cs.version_keyctr
  where v.active_ = 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $activeIndicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // writeLog('active indicators was: ');
  // writeLog($activeIndicators);

  // Fetch taken permissions by other users in user_roles_barangay (assessment)
  // this excludes permissions taken by the given userID
  // $sql = "SELECT  b.brgyid, b.brgyname, p.* from permissions p inner join user_roles_barangay rb on rb.permission_id = p.id inner join refbarangay b on b.brgyid = rb.barangay_id";
  $takenAssessment = [];
  $sql = "SELECT 
    b.brgyid, 
    p.*
FROM refbarangay b
JOIN user_roles_barangay urb ON b.brgyid = urb.barangay_id
JOIN permissions p ON urb.permission_id = p.id
WHERE urb.user_id != :uid
";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':uid' => $userID]);
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    writeLog('original row');
    writeLog($row);
    $bid = $row['brgyid'];
    // remove int values (like p.id)
    unset($row['brgyid'], $row['id'], $row['last_modified']);
    // writeLog('Filtered row');
    // writeLog($filteredRow);
    // get assessment perms
    $assessmentData = array_keys(array_filter($row, fn($v, $k) => str_contains($k, 'assessment') && (int)$v == 1, ARRAY_FILTER_USE_BOTH));
    // writeLog('assessment data row');
    // writeLog($assessmentData);
    $takenAssessment[$bid] = $assessmentData;
  }
  writeLog('taken assessment permissions was:');
  writeLog($takenAssessment);

  $response = [];

  // get the permission names only 
  $rolePermissions = array_keys($barPerms);
  writeLog('orig bar perms:');
  writeLog($barPerms);

  // remove allow_bar and get only the assessment permissions
  $rolePermissions = array_filter($rolePermissions, function ($key) {
    return !str_contains($key, 'allow') && !str_contains($key, 'barangay') && str_contains($key, 'assessment');
  });

  // current user perms per barangay
  $userAssessmentPerms = [];
  if (!empty($userID)) {
    $sql = "SELECT 
    b.brgyid, 
    p.*
FROM refbarangay b 
JOIN user_roles_barangay urb ON b.brgyid = urb.barangay_id 
JOIN permissions p ON urb.permission_id = p.id 
WHERE urb.user_id = :uid 
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $userID]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      // writeLog('original row');
      // writeLog($row);
      $bid = $row['brgyid'];
      // remove int values (like p.id)
      unset($row['brgyid'], $row['id'], $row['last_modified']);
      // writeLog('Filtered row');
      // writeLog($filteredRow);
      // get assessment perms
      $assessmentData = array_keys(array_filter($row, fn($v, $k) => str_contains($k, 'assessment') && (int)$v == 1, ARRAY_FILTER_USE_BOTH));
      // writeLog('assessment data row');
      // writeLog($assessmentData);
      $userAssessmentPerms[$bid] = $assessmentData;
    }
  }
  writeLog('user assessment permissions was:');
  writeLog($userAssessmentPerms);


  // Build the JSON response
  foreach ($barangays as $barangay) {
    foreach ($activeIndicators as $indicator) {
      // Get available assessment permissions (exclude taken ones)
      // $takenPermissions = array_filter($allPermissions, $takenAssessment[$bid]);
      $takenPermissions = $takenAssessment[$barangay['brgyid']] ?? [];
      $curUserPerms = $userAssessmentPerms[$barangay['brgyid']] ?? [];
      // writeLog('Taken permissions after: ');
      // writeLog($takenPermissions);

      // TODO: add a ternary to check if it is taken or not.
      // CREATE MODE: if taken, mark with check and disable
      // EDIT MODE: if taken and user id match, mark with check. If taken and not user match, then check and disable
      $response[] = [
        "barangay" => [
          'name' => $barangay['brgyname'],
          'id' => $barangay['brgyid'],
        ],
        "indicators" => $indicator,
        // remove 'assessment' prefix on all perms
        'taken_perms' => array_map(function ($perm) {
          return str_replace('assessment_', '', $perm);
        }, $takenPermissions),
        'current_perms' => array_map(function ($perm) {
          return str_replace('assessment_', '', $perm);
        }, $curUserPerms),
        'available_perms' => array_map(function ($perm) {
          return str_replace('assessment_', '', $perm);
        }, $rolePermissions),
      ];
    }
  }

  writeLog('Final result was:');
  writeLog($response);
  // Return JSON response
  echo json_encode($response, JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($th);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
