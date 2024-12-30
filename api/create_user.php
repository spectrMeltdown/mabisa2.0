<?php

declare(strict_types=1);
require_once '../models/user_model.php';
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $fullName = $_POST['fullName'];
  /** @var string */
  $username = $_POST['username'];
  /** @var string */
  $email = $_POST['email'];
  /** @var string */
  $mobileNum = $_POST['mobileNum'];
  /** @var string */
  $pass = $_POST['pass'];
  /** @var string */
  $confirmPass = $_POST['confirmPass'];
  /** @var string */
  $role = $_POST['role'];

  // construct new user
  $user = new User('', $username, $fullName, $barangay, $email, (int)$mobileNum, $pass, false, $role);

  // insert to database
  global $pdo;
  $sql = 'insert into (username)';
  try {
    $stmt = $pdo->prepare($sql);
  } catch (\Throwable $th) {
    //throw $th;
  }
}