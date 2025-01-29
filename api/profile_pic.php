<?php

declare(strict_types=1);
// header('Content-Type: application/json; charset=utf-8');
// ini_set('display_errors', 0); // Disable error display
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('log_errors', 1);    // Enable error logging
require_once '../db/db.php';
require 'logging.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405); // Method Not Allowed
  echo 'Invalid request method.';
  exit;
}

if (!isset($_COOKIE['id'])) {
  http_response_code(400);
  echo 'User ID is not set.';
  exit;
}

$userId = $_COOKIE['id'];

if (!isset($_FILES['profileImage'])) {
  http_response_code(400);
  echo 'No file uploaded.';
  exit;
}

$file = $_FILES['profileImage'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  echo 'Error during file upload.';
  exit;
}

// Validate file size (<= 2MB)
if ($file['size'] > 2 * 1024 * 1024) {
  http_response_code(400);
  echo 'File size must not exceed 2MB.';
  exit;
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
  http_response_code(400);
  echo 'Invalid file type.';
  exit;
}

// Process the file (e.g., move to a permanent location)
$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'profile_' . $userId . '.' . $extension;
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
  http_response_code(500);
  echo 'Failed to save the file.';
  exit;
}

// Update the database
try {
  global $pdo;
  $stmt = $pdo->prepare('UPDATE users SET profile_pic = :image WHERE id = :id');
  $stmt->execute([':image' => $filePath, ':id' => $userId]);
} catch (Exception $e) {
  http_response_code(500);
  echo 'Database error: ' . $e->getMessage();
  exit;
}

http_response_code(200); // Success
