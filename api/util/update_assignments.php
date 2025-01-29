<?php

function updateUserAssignments(string $auditorId, ?string $auditorBarangays, PDO $pdo)
{
  if ($auditorBarangays) {
    $auditorBarangays = json_decode($auditorBarangays, true);

    if (json_last_error() === JSON_ERROR_NONE) {
      print_r($auditorBarangays);
    } else {
      echo "Invalid JSON";
    }

    $sql = 'update user_assignments set auditor = :auditorId where brgyid = :brgyid;';
    $stmt = $pdo->prepare($sql);
    foreach ($auditorBarangays as $key => $value) {
      $stmt->execute([
        ':auditorId' => $auditorId,
        ':brgyid' => $value,
      ]);
    }
  } else {
    writeLog('Not a auditor, skipping update of user assignments.');
  }
}
