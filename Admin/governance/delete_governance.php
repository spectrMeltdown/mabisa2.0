<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Delete the governance entry
    $stmt = $pdo->prepare("DELETE FROM maintenance_governance WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Governance entry deleted successfully!";
    header('Location: index_governance.php');
    exit();
}
?>
