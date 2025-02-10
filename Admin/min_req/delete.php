<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_area_mininumreqs WHERE keyctr = ?");
        if ($stmt->execute([$keyctr]) && $stmt->rowCount() > 0) {
            $pdo->commit();
            $_SESSION['success'] = "Minimum Requirement deleted successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to delete: No matching record found.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting record: " . $e->getMessage();
    }

    header("Location: index.php");
    exit;
}
?>
