<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Delete the record
        $stmt = $pdo->prepare("DELETE FROM maintenance_area_mininumreqs_sub WHERE keyctr = ?");
        if ($stmt->execute([$keyctr]) && $stmt->rowCount() > 0) {
            // Commit the transaction
            $pdo->commit();
            $_SESSION['success'] = "Sub Requirement deleted successfully!";
        } else {
            // Rollback if deletion fails
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to delete: No matching record found.";
        }
    } catch (Exception $e) {
        // Rollback in case of an exception
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting record: " . $e->getMessage();
    }
}

// Redirect back
header("Location: index.php");
exit;
?>
