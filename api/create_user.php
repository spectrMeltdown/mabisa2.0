<?php

declare(strict_types=1);
require_once '../models/user_model.php';
require_once '../db/db.php';
require_once 'logging.php';

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

  //check if passwords match
  // if ($pass != $confirmPass) {
  //   die('Passwords do not match!!');
  // }
  // echo $mobileNum;
  // construct new user
  $user = new User('', $username, $fullName, $barangay, $email, (int)$mobileNum, $pass, false, $role);

  // insert to database
  global $pdo;
  $sql = 'insert into user_policy (username, fullName, email, mobileNo, password, accessLevel, barangay) values (:username, :fullname, :email, :mobileNum, :password, :role, :barangay)';
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':username' => $user->username,
      ':fullname' => $user->fullName,
      ':email' => $user->email,
      ':mobileNum' => $user->mobileNum,
      ':password' => password_hash($user->password, PASSWORD_BCRYPT),
      ':role' => $user->role,
      ':barangay' => $user->barangay,
    ]);
    // blank means everything went okay
    echo '';
  } catch (\Throwable $th) {
    echo  $th;
  }
}
