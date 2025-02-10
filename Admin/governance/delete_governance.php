<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_governance WHERE keyctr = :keyctr");
        $stmt->execute(['keyctr' => $keyctr]);

        $pdo->commit();

        $_SESSION['success'] = "Governance entry deleted successfully!";
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
        header('Location: index.php');
        exit();
    }
}
?>
