<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
require_once 'logging.php';
require_once '../db/db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request method.');
  if (empty($_POST['id'])) throw new Exception('No ID given.');

  // get post vars
  /** @var int|string */
  $id = $_POST['id'];
  /** @var string */
  $fullName = !empty($_POST['fullName']) ? trim($_POST['fullName']) : null;
  /** @var string */
  $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
  /** @var string */
  $email = !empty($_POST['email']) ?  trim($_POST['email']) : null;
  /** @var string */
  $mobileNum = !empty($_POST['mobileNum']) ? substr(trim((string)$_POST['mobileNum']), 3) : null;
  /** @var string */
  $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : null;

  // if self was specified, use cookie id
  if ($id == 'self') $id = $_COOKIE['id'];

  // initialize other vars
  global $pdo;
  $sql = 'UPDATE users SET ';
  $params = [':id' => $id];

  // append field(s) to update and set params
  if ($fullName) {
    $sql .= 'full_name = :fullName, ';
    $params[':fullName'] = $fullName;
  }
  if ($username) {
    $sql .= 'username = :username, ';
    $params[':username'] = $username;
  }
  if ($email) {
    $sql .= 'email = :email, ';
    $params[':email'] = $email;
  }
  if ($mobileNum) {
    $sql .= 'mobile_num = :mobileNum, ';
    $params[':mobileNum'] = $mobileNum;
  }
  if ($pass) {
    $sql .= 'password = :pass, ';
    $params[':pass'] = password_hash($pass, PASSWORD_BCRYPT);
  }

  // remove trailing space and comma on sql statement
  $sql = substr($sql, 0, -2);

  // append the id
  $sql .= ' where id = :id;';

  // EXECUTE
  writeLog('statement was:');
  writeLog($sql);
  writeLog('params was:');
  writeLog($params);
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
} catch (\Throwable $th) {
  http_response_code(500);
  writeLog($th);
  echo json_encode($th->getMessage());
}
