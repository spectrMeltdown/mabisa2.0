<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_area_description WHERE keyctr = :keyctr");
        $stmt->execute(['keyctr' => $keyctr]);

        $pdo->commit();

        $log->userLog('Deleted an Area Description with id:' . $keyctr);
        $_SESSION['success'] = "Description deleted successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting description: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
} else {
    echo "Invalid request!";
}
