<?php

declare(strict_types=1);
require_once '../models/user_model.php';
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $id = $_POST['id'];
  /** @var string */
  $role = $_POST['role'];
  /** @var string */
  $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'N/A';

  // insert to database
  global $pdo;
  $sql = 'update user_policy set role=:role, barangay=:barangay where id=:id;';
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':id' => $id,
      ':role' => $role,
      ':barangay' => $barangay,
    ]);
    echo 'Success! You may now close this tab.';
  } catch (\Throwable $th) {
    echo  $th;
  }
}