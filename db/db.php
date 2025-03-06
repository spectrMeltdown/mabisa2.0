<?php

declare(strict_types=1);
$dbHost = 'localhost';
$db = 'mabisa2.0';
$dbUser = 'root'; // Change if you have a different username
$dbPass = ''; // Change if you have a different password

try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$db;charset=utf8mb4", $dbUser, $dbPass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  die("ERROR: Unable to connect to database.");
}
