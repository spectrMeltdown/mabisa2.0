<?php
require 'api/logging.php';
if (empty($_COOKIE['id'])) {
    header('Location: Admin/login.php');
    exit;
}
header('Location: Admin/dashboard.php');
