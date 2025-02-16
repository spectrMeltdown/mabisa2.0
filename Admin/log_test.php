<?php 
require_once '../api/audit_log.php';
require_once '../db/db.php';

$logging = new Audit_log($pdo);


$userId='1';
$userName = 'admin';
$action = 'Willow the gay';

$stmt = $logging->userLog($userId, $userName, $action);
$stmt1 = $pdo->prepare($stmt);


?>