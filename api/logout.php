<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);    // Enable error logging
require_once 'logging.php';
require_once '../db/db.php';
require_once '../api/audit_log.php';
$log = new Audit_log($pdo);
try {
  // set to a past date to expire the cookie
  setcookie('id', '', time() - 3600, '/');
  $log->userLog('User Logged Out'); //logging
  writeLog('logout complete');
} catch (Exception $e) {
  http_response_code(500); // Set HTTP 500 response code
  writeLog($e->getMessage());
  echo json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT);
}
