<?php

declare(strict_types=1);

function loadEnvFile(string $path): void
{
  if (!is_file($path)) {
    return;
  }
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if ($lines === false) {
    return;
  }
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
      continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);
    if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
      $value = substr($value, 1, -1);
    }
    putenv($key . '=' . $value);
    $_ENV[$key] = $value;
  }
}

loadEnvFile(__DIR__ . '/../../.env');

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$db = getenv('DB_NAME') ?: 'mabisa2.0';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
  $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$db;charset=utf8mb4", $dbUser, $dbPass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  die("ERROR: Unable to connect to database.");
}
