<?php

declare(strict_types=1);
$host = 'localhost';
$db = 'mabisa';
$user = 'root'; // Change if you have a different username
$pass = ''; // Change if you have a different password

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
