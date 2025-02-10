<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once '../db/db.php';
require_once 'logging.php';
require 'util/update_assignments.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  /** @var string */
  $id = $_POST['id'];
  /** @var string */
  $fullName = trim($_POST['full_name']);
  /** @var string */
  $username = trim($_POST['username']);
  /** @var string */
  $email = trim($_POST['email']);
  /** @var string */
  $mobileNum = substr(trim($_POST['mobile_num']), 3);
  /** @var string */
  $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : null;
  // /** @var string */
  // $confirmPass = trim($_POST['confirmPass']);
  /** @var string */
  $role = $_POST['role'];
  /** @var string */
  $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'N/A';
  $auditorBarangays = $_POST['auditorBarangays'] ?? null;

  // writeLog($role);
  // insert to database
  global $pdo;
  $sql = 'update users set username = :username, full_name = :full_name, email = :email, mobile_num = :mobile_num, role = :role, barangay = :barangay';
  $parameters = [
    ':id' => $id,
    ':username' => $username,
    ':full_name' => $fullName,
    ':email' => $email,
    ':mobile_num' => $mobileNum,
    ':role' => $role,
    ':barangay' => $barangay,
  ];
  if ($pass !== null) {
    $sql .= ', password = :password';
    $parameters[':password'] = password_hash($password, PASSWORD_BCRYPT);
  }
  $sql .= ' where id = :id;';

  try {
    // update user_assignments table
    // updateUserAssignments($id, $auditorBarangays, $pdo);
    writeLog($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);
  } catch (\Throwable $th) {
    http_response_code(500);
    $message = $th->getMessage();
    writeLog($message);
    echo json_encode($message);
  }
}
