<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once '../db/db.php';
require_once 'logging.php';
require 'util/update_assignments.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $fullName = trim($_POST['full_name']);
  /** @var string */
  $username = trim($_POST['username']);
  /** @var string */
  $email = trim($_POST['email']);
  /** @var string */
  $mobileNum = trim($_POST['mobile_num']);
  /** @var string */
  $pass = trim($_POST['pass']);
  // /** @var string */
  // $confirmPass = trim($_POST['confirmPass']);
  /** @var string */
  $role = $_POST['role'];
  /** @var string */
  $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'N/A';
  $auditorBarangays = $_POST['auditorBarangays'] ?? null;

  //check if passwords match
  // if ($pass != $confirmPass) {
  //   die('Passwords do not match!!');
  // }
  // echo $mobileNum;
  // construct new user

  // insert to database
  global $pdo;
  $sql = 'insert into users (username, full_name, email, mobile_num, password, role, barangay) values (:username, :full_name, :email, :mobile_num, :password, :role, :barangay)';
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':username' => $username,
      ':full_name' => $fullName,
      ':email' => $email,
      ':mobile_num' => substr($mobileNum, 2),
      ':password' => password_hash($password, PASSWORD_BCRYPT),
      ':role' => $role,
      ':barangay' => $barangay,
    ]);
    updateUserAssignments($id, $auditorBarangays, $pdo);
    // blank means everything went okay
  } catch (\Throwable $th) {
    echo json_encode($th);
  }
}
