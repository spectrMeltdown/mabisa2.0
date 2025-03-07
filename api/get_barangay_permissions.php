<?php

declare(strict_types=1);
$useAsImport; // for get_permissions.php
$permsOnly = false;
$disableLogging = true; // set to true to disable for this file
require_once 'logging.php';
require_once '../db/db.php';
require_once 'get_all_perm_cols.php';
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
  $sql = "SELECT i.keyctr AS id, i.indicator_code AS code, mr.relevance_definition AS description
  FROM maintenance_area_indicators i 
  JOIN maintenance_criteria_setup cs ON cs.indicator_keyctr = i.keyctr 
  JOIN maintenance_criteria_version v ON v.keyctr = cs.version_keyctr
  JOIN maintenance_area_mininumreqs mr ON mr.keyctr = cs.minreqs_keyctr
  WHERE v.active_ = 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $activeIndicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // make sure ids are unique
  $activeIndicators = array_values(array_intersect_key($activeIndicators, array_unique(array_column($activeIndicators, 'id'))));

  // filter out duplicate indicators

  // writeLog('active indicators was: ');
  // writeLog($activeIndicators);

  /* 
  Fetch taken permissions by other users in user_roles_barangay (assessment)
  include_once only the following:
  - permissions that match the same role
  - permissions that match the barangay & indicator
  */
  $takenAssessment = [];
  $sql = "SELECT 
    urb.barangay_id as brgyid,
    urb.indicator_id as indid,
    p.*
FROM user_roles_barangay urb
JOIN users u ON urb.user_id 
JOIN roles r ON r.id = u.role_id 
JOIN permissions p ON urb.permission_id = p.id
WHERE urb.user_id != :uid AND r.id = :roleID
";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':uid' => $userID, ':roleID' => $roleID]);
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    writeLog('original row');
    writeLog($row);
    $bid = $row['brgyid'];
    $iid = $row['indid'];
    // remove int values (like p.id)
    unset($row['brgyid'], $row['indid'], $row['id'], $row['last_modified']);
    // writeLog('Filtered row');
    // writeLog($filteredRow);

    // get assessment perms
    $assessmentData = array_keys(array_filter($row, fn($v, $k) => str_contains($k, 'assessment') && $v == 1, ARRAY_FILTER_USE_BOTH));
    // writeLog('assessment data row');
    // writeLog($assessmentData);

    // filter out 
    // if (!isset($takenAssessment[$bid][$iid])) $takenAssessment[$bid][$iid] = [];
    $takenAssessment[$bid][$iid] = $assessmentData;
  }
  writeLog('taken assessment permissions was:');
  writeLog($takenAssessment);

  $response = [];

  // get the permission names only 
  $rolePermissions = array_keys($barPerms);
  writeLog('orig bar perms of user:');
  writeLog($barPerms);

  // remove allow_bar and get only the assessment permissions
  $rolePermissions = array_filter($rolePermissions, function ($key) {
    return !str_contains($key, 'allow') && !str_contains($key, 'barangay') && str_contains($key, 'assessment');
  });

  // current user perms per barangay
  $userAssessmentPerms = [];
  if (!empty($userID)) {
    $sql = "SELECT 
    urb.barangay_id as brgyid,
    urb.indicator_id as indid,
    p.*
FROM user_roles_barangay urb 
JOIN users u ON urb.user_id 
JOIN roles r ON r.id = u.role_id 
JOIN permissions p ON urb.permission_id = p.id 
WHERE urb.user_id = :uid AND r.id = :roleID
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $userID, ':roleID' => $roleID]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      // writeLog('original row');
      // writeLog($row);
      $bid = $row['brgyid'];
      $iid = $row['indid'];
      // remove int values (like p.id)
      unset($row['brgyid'], $row['indid'], $row['id'], $row['last_modified']);
      // writeLog('Filtered row');
      // writeLog($filteredRow);
      // get assessment perms
      $assessmentData = array_keys(array_filter($row, fn($v, $k) => str_contains($k, 'assessment') && $v == 1, ARRAY_FILTER_USE_BOTH));
      // writeLog('assessment data row');
      // writeLog($assessmentData);
      // if (!isset($userAssessmentPerms[$bid][$iid])) $userAssessmentPerms[$bid][$iid] = [];
      $userAssessmentPerms[$bid][$iid] = $assessmentData;
    }
  }
  writeLog('user assessment permissions was:');
  writeLog($userAssessmentPerms);


  // Build the JSON response
  foreach ($barangays as $barangay) {
    foreach ($activeIndicators as $indicator) {
      // Get available assessment permissions (exclude taken ones)
      // $takenPermissions = array_filter($allPermissions, $takenAssessment[$bid]);
      $takenPermissions = $takenAssessment[$barangay['brgyid']][$indicator['id']] ?? [];
      $curUserPerms = $userAssessmentPerms[$barangay['brgyid']][$indicator['id']] ?? [];
      // writeLog('Taken permissions after: ');
      // writeLog($takenPermissions);

      writeLog('ROW RESULT: ');
      writeLog($barangay);
      writeLog('taken perms: ');
      writeLog($takenPermissions);
      writeLog('current user perms: ');
      writeLog($curUserPerms);
      writeLog('available role perms: ');
      writeLog($rolePermissions);

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

  // writeLog('Final result was:');
  // writeLog($response);
  // Return JSON response
  echo json_encode($response, JSON_PRETTY_PRINT);
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($th);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
