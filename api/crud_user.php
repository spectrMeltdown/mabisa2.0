<?php
// Step 1: Fetch all barangays
$sql = "SELECT brgyid, name FROM refbarangay";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 2: Fetch all permissions
$sql = "SELECT id, name FROM permissions";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$allPermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$permissionsMap = [];
foreach ($allPermissions as $perm) {
  $permissionsMap[$perm['id']] = $perm['name'];
}

// Step 3: Fetch taken permissions in user_roles (criteria)
$sql = "SELECT barangay_id, permission_id FROM user_roles";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$takenCriteria = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $takenCriteria[$row['barangay_id']][] = $row['permission_id'];
}

// Step 4: Fetch taken permissions in user_roles_barangay (assessment)
$sql = "SELECT barangay_id, permission_id FROM user_roles_barangay";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$takenAssessment = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $takenAssessment[$row['barangay_id']][] = $row['permission_id'];
}

// Step 5: Build the JSON response
$response = [];
foreach ($barangays as $barangay) {
  $barangayId = $barangay['brgyid'];

  // Get available criteria permissions (exclude taken ones)
  $availableCriteria = array_diff(array_keys($permissionsMap), $takenCriteria[$barangayId] ?? []);

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
