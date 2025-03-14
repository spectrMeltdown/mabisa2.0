<?php
require_once '../db/db.php';

try {
  // if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  global $pdo;

  $permissions = [
    'assessment_submissions_read',
    'assessment_comments_create',
    'assessment_comments_read',
    'assessment_comments_update',
    'assessment_comments_delete',
    'assessment_submissions_approve_disapprove',
  ];

  // create permissions
  $sql = 'INSERT INTO permissions (';
  foreach ($permissions as $perm) {
    $sql .= ' ' . $perm . ',';
  }
  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ') VALUES (';

  // attach values
  $true = array_fill(0, count($permissions), '?');
  foreach ($true as $value) {
    $sql .= ' ' . $value . ',';
  }

  // remove trailing comma
  $sql = rtrim($sql, ',');
  $sql .= ')';


  $pdo->beginTransaction();
  // insert permissions
  $exec = array_fill(0, count($permissions), '1');
  // echo var_dump($exec);
  // echo '<br>';
  // echo $sql;
  // echo '<br>';
  $stmt = $pdo->prepare($sql);
  $stmt->execute($exec);
  $perm_id = $pdo->lastInsertId();

  // create user & get id
  $stmt = $pdo->prepare('INSERT INTO users (full_name, username, email, password, role_id, mobile_num) VALUES (?, ?, ?, ?, ?, ?)');
  $stmt->execute(['Master Auditor', 'aud', 'aud@gmail.com', password_hash('auditor', PASSWORD_BCRYPT), 28, '9123456780']);
  $user_id = $pdo->lastInsertId();

  // get all barangays
  $stmt = $pdo->query('SELECT brgyid AS bid FROM refbarangay');
  $bars = $stmt->fetchAll();
  echo 'Bars is: ';
  foreach ($bars as $bar) {
    echo var_dump($bar);
    echo '<br>';
  }

  // get all indicators (doesn't consider active version cuz i'm lazy rn)
  $stmt = $pdo->query('SELECT mai.keyctr AS iid FROM maintenance_area_indicators mai JOIN maintenance_criteria_setup mcs ON mcs.indicator_keyctr = mai.keyctr');
  $inds = $stmt->fetchAll();
  // echo 'Inds is: ';
  // echo $inds;
  $inds = array_unique($inds, SORT_REGULAR);
  foreach ($inds as $ind) {
    // echo 'per item';
    // echo var_dump($ind);
    // echo '<br>';
  }

  // create new user_roles_barangay
  foreach ($bars as $bar) {
    foreach ($inds as $ind) {
      // insert
      $stmt = $pdo->prepare('INSERT INTO user_roles_barangay (user_id, barangay_id, indicator_id, permission_id) VALUES (?, ?, ?, ?)');
      $stmt->execute([$user_id, $bar['bid'], $ind['iid'], $perm_id]);
    }
  }
  $pdo->commit();
  echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaa';
} catch (\Throwable $th) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo $th;
}
