<?php
require 'db.php';

$keyctr = $_GET['keyctr'];

$stmt = $pdo->prepare("DELETE FROM maintenance_area_mininumreqs_sub WHERE keyctr=?");
$stmt->execute([$keyctr]);

header("Location: index.php");
exit();
?>
