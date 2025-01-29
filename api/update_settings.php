<?php
if ($id === 'self' && isset($_COOKIE['id'])) {
  $id = $_COOKIE['id'];
}
if ($id === 'self' && isset($_COOKIE['id'])) {
  $id = $_COOKIE['id'];
} else {
  throw new Exception('self id not found!');
}
$sql = 'SELECT * FROM users WHERE username=:username, password=:password  LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
