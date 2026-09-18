<?php
require_once 'app/api/logging.php';
if (empty($_COOKIE['id'])) {
    header('Location: app/Admin/login.php');
    exit;
}
header('Location: app/Admin/dashboard.php');
