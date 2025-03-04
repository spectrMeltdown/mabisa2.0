<?php

declare(strict_types=1);
// ini_set('display_errors', 0); // Disable error display
// header('Content-Type: application/json');
require_once 'logging.php';
require_once '../db/db.php';

writeLog('IN MARK READ');
try {
  if ($_SERVER['REQUEST_METHOD'] != 'GET') throw new Exception('Invalid request.');
  if (empty($_GET['id'])) throw new Exception('ID missing.');

  /** @var string */
  $id = $_GET['id'];
  writeLog($id);

  // update mark as read
  $pdo->beginTransaction();
  $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id=?');
  $stmt->execute([$id]);
  $pdo->commit();
} catch (\Throwable $th) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  http_response_code(500);
  $message = $th->getMessage();
  writeLog($th);
  echo json_encode($message, JSON_PRETTY_PRINT);
}
