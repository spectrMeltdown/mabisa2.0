<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';
require_once 'has_permissions.php';

try {
  if ($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exception('Invalid request.');
  // redirect if logged out
  if (empty($_COOKIE['id'])) {
    header('location: ' . $_SERVER['HTTP_HOST'] . '/Admin/logged_out');
    exit;
  }

  /** @var string */
  $title = trim($_POST['title']);
  /** @var string */
  $message = trim($_POST['message']);
  /** @var string */
  $roleID = empty($_POST['role_id']) ? null : $_POST['role_id'];
  /** @var string */
  $brgyID = empty($_POST['brgy_id']) ? null : $_POST['brgy_id'];

  if ($brgyID != null) {
    $stmt = $pdo->prepare('SELECT role_id FROM users WHERE brgy_id = :brgy_id');
    $stmt->execute([':brgy_id' => $brgyID]);
    $userIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
} catch (\Throwable $th) {
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($message);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
