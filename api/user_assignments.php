<?php

declare(strict_types=1);
// error_reporting(E_ALL ^ E_NOTICE);
// ini_set('display_errors', 0); // Disable error display
// ini_set('log_errors', 1);    // Enable error logging
require_once '../db/db.php';
require 'logging.php';

try {
  global $pdo;

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid method.');
  }

  $sql;
  $params = [];
  if (!empty($_POST['id'])) {
    $id;
    $sql = 'select brgyid, brgyname, auditor 
    from refbarangay 
    inner join users 
    on refbarangay.auditor = users.id 
    where users.id = :id';

    if ($_POST['id'] === 'self') {
      $id = $_COOKIE['id'];
    } else {
      $id = $_POST['id'];
    }

    $params[':id'] = $id;
  } else {
    $sql = 'select brgyid, brgyname, auditor 
    from refbarangay 
    where auditor is null';
  }

  $stmt;
  if ($params) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  } else {
    $stmt = $pdo->query($sql);
  }
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (isset($customUserID)) {
    return $result;
  }
  echo json_encode($result);
} catch (Exception $e) {
  writeLog('Exception!' . $e);
  echo json_encode(['error' => $e->getMessage()]);
}
