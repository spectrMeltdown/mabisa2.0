<?php

declare(strict_types=1);
require_once '../db/db.php';
// ini_set('display_errors', 0); // Disable error display
// require_once '../auth/check_permissions.php'; // Ensure this checks admin privileges

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Ensure `id` is passed in the request
  if (empty($_POST['id'])) {
    http_response_code(400); // Bad Request
    echo 'Missing user ID.';
    exit;
  }

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
  $sql = 'DELETE FROM user_policy WHERE id = :id';

  try {
    $stmt = $pdo->prepare($sql);
    // bindParam is same sa execute, only this allows specifying data type. 
    // use execute for passing array i.e less verbose, however data types not checked
    $stmt->bindParam(':id', $userId, PDO::PARAM_STR);

    if ($stmt->execute()) {
      if ($stmt->rowCount() > 0) {
        echo ''; // Blank response indicates success
      } else {
        http_response_code(404); // Not Found
        echo 'User not found or already deleted.';
      }
    } else {
      http_response_code(500); // Internal Server Error
      echo 'Failed to delete user. Please try again.';
    }
  } catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo 'An error occurred: ' . $e->getMessage();
  }
}
