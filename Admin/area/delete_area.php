<?php
include '../../db/db.php';
include '../../api/audit_log.php';
session_start();

$log = new Audit_log($pdo);

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare("DELETE FROM maintenance_area_description WHERE keyctr = :keyctr");
        $stmt1->execute(['keyctr' => $keyctr]);

        $stmt2 = $pdo->prepare("DELETE FROM maintenance_area WHERE keyctr = :keyctr");
        $stmt2->execute(['keyctr' => $keyctr]);

        $pdo->commit();
        $log->userLog('Deleted an Area with id: ' . $keyctr);

        $_SESSION['success'] = "Area deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
}

header('Location: index.php');
exit();
?>
