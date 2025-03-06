<?php

declare(strict_types=1);
require_once 'logging.php';
require_once '../db/db.php';

try {
  writeLog('IN GET ALL NOTIF');

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') throw new Exception('Invalid request.');
  if (empty($_COOKIE['id'])) throw new Exception('Not logged in.');
  if (!isset($_GET['page']) || !ctype_digit($_GET['page'])) throw new Exception('Invalid page count.');

  $limit = 10;
  $page = (int)$_GET['page'];
  $offset = $page * $limit;
  $userId = (int) $_COOKIE['id']; // Ensure integer ID

  $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :id ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
  $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
  $stmt->execute();

  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($notifications);
} catch (\Throwable $th) {
  writeLog($th);
  echo json_encode(["error" => $th->getMessage()]);
}
