<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once '../db/db.php';
require_once 'logging.php';
require 'util/update_assignments.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $fullName = trim($_POST['fullName']);
  /** @var string */
  $username = trim($_POST['username']);
  /** @var string */
  $email = trim($_POST['email']);
  /** @var string */
  $mobileNum = trim($_POST['mobileNum']);
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
  $sql = 'insert into user_policy (username, fullName, email, mobileNum, password, role, barangay) values (:username, :fullname, :email, :mobileNum, :password, :role, :barangay)';
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':username' => $username,
      ':fullname' => $fullName,
      ':email' => $email,
      ':mobileNum' => substr($mobileNum, 2),
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
