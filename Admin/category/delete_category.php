<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM maintenance_category WHERE code = ?");

        if ($stmt->execute([$code])) {

            $pdo->commit();
            $log->userLog('Deleted a Category with Code: ' . $code);
            $_SESSION['success'] = "Category deleted successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to delete category.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }


    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: index.php");
    exit;
}
