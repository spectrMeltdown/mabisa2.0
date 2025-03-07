<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_area_mininumreqs_sub WHERE keyctr = ?");
        if ($stmt->execute([$keyctr]) && $stmt->rowCount() > 0) {

            $pdo->commit();
            $log->userLog('Deleted a Sub Requirement with ID: ' . $keyctr);
            $_SESSION['success'] = "Sub Requirement deleted successfully!";
        } else
            $pdo->rollBack();
        $_SESSION['error'] = "Failed to delete: No matching record found.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting record: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
