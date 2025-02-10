<?php
require '../../db/db.php';
session_start();

$keyctr = $_GET['keyctr'];

try {
    $pdo->beginTransaction();

    $sql = "DELETE FROM maintenance_document_source WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyctr' => $keyctr]);

    $pdo->commit();

    $_SESSION['success'] = "Document Source deleted successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error deleting document source: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>
