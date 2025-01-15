<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';
require_once '../models/role_model.php';
require_once 'logging.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $id = $_POST['id'];
  /** @var string */
  $fullName = trim($_POST['fullName']);
  /** @var string */
  $username = trim($_POST['username']);
  /** @var string */
  $email = trim($_POST['email']);
  /** @var string */
  $mobileNum = trim($_POST['mobileNum']);
  /** @var string */
  $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : null;
  // /** @var string */
  // $confirmPass = trim($_POST['confirmPass']);
  /** @var string */
  $role = $_POST['role'];
  /** @var string */
  $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'N/A';

  // insert to database
  global $pdo;
  $sql = 'update user_policy set username = :username, fullName = :fullName, email = :email, mobileNo = :mobileNum, accessLevel = :role, barangay = :barangay';
  $parameters = [
    ':id' => $id,
    ':username' => $username,
    ':fullname' => $fullName,
    ':email' => $email,
    ':mobileNum' => $mobileNum,
    ':role' => getAccessLevel($role),
    ':barangay' => $barangay,
  ];
  if ($pass !== null) {
    $sql .= ', password=:password';
    $parameters[':password'] = password_hash($password, PASSWORD_BCRYPT);
  }
  $sql .= ' where id = :id;';
  try {
    writeLog($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);
  } catch (\Throwable $th) {
    echo  $th;
  }
}
