<?php

declare(strict_types=1);
require_once '../db/db.php';
require_once 'logging.php';
require_once '../api/audit_log.php';
// ini_set('display_errors', 0); // Disable error display
// require_once 'auth/check_permissions.php'; // Ensure this checks admin privileges

try {
  $log = new Audit_log($pdo);
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request');
  if (empty($_POST['id'])) throw new Exception('Missing user ID.');

  /** @var int */
  $userId = trim($_POST['id']);

  // Validate UUID format using a regex
  // if (!preg_match('/^[a-f0-9\-]{36}$/i', $userId)) {
  //   http_response_code(400); // Bad Request
  //   echo 'Invalid user ID format.';
  //   exit;
  // }

  // Ensure the user has proper authorization
  // if (!checkAdminPermission()) { // Adjust based on your authentication logic
  //   http_response_code(403); // Forbidden
  //   echo 'You are not authorized to delete users.';
  //   exit;
  // }

  global $pdo;

  // proceed only if there is more than one admin available
  $sql = 'SELECT COUNT(*) FROM users WHERE id != :id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $userId]);
  $users = $stmt->fetch();
  if ($users == 0) throw new Exception('You are the last admin. Your account cannot be deleted.');

  // delete permissions (safe because users will set null on permission id)
  $sql = '
  DELETE p
FROM permissions p
JOIN user_roles ur ON p.permission_id = ur.permission_id
JOIN user_roles_barangay urb ON p.permission_id = urb.permission_id
WHERE ur.user_id = :id OR urb.user_id = :id;';
  $pdo->beginTransaction();
  $stmt = $pdo->prepare($sql);


  // delete the user (will also delete entries in user_roles and user_roles_barangay)
  $sql = 'DELETE FROM users WHERE id = :id';

  $pdo->beginTransaction();
  $stmt = $pdo->prepare($sql);
  // bindParam is same sa execute, only this allows specifying data type. 
  // use execute for passing array i.e less verbose, however data types not checked
  $stmt->bindParam(':id', $userId, PDO::PARAM_STR);

  if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
      $log->userLog('Deleted a User with ID: ' . $userId); //logging
      echo ''; // Blank response indicates success
    } else {
      http_response_code(404); // Not Found
      echo 'User not found or already deleted.';
    }
  } else {
    http_response_code(500); // Internal Server Error
    echo 'Failed to delete user. Please try again.';
  }
  $pdo->commit();
} catch (\Throwable $th) {
  http_response_code(500); // Internal Server Error
  writeLog($e);
  echo 'An error occurred: ' . $e->getMessage();
}
