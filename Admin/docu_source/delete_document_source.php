<?php
require 'db.php';

$keyctr = $_GET['keyctr'];

$sql = "DELETE FROM maintenance_document_source WHERE keyctr = :keyctr";
$stmt = $pdo->prepare($sql);
$stmt->execute(['keyctr' => $keyctr]);

header('Location: index.php');
?>
