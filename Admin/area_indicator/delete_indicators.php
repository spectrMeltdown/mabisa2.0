<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Delete the record
        $stmt = $pdo->prepare("DELETE FROM maintenance_area_indicators WHERE keyctr = ?");
        if ($stmt->execute([$keyctr])) {
            // Commit the transaction
            $pdo->commit();
            $_SESSION['success'] = "Indicator entry deleted successfully!";
        } else {
            // Rollback if deletion fails
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to delete indicator entry.";
        }
    } catch (Exception $e) {
        // Rollback in case of an exception
        $pdo->rollBack();
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }

    header("Location: index.php");
    exit;
}
