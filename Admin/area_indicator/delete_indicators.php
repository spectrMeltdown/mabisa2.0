<?php
include 'db.php';
session_start();

if (isset($_GET['indicator_code'])) {
    $indicator_code = $_GET['indicator_code'];

    // Delete the record
    $stmt = $pdo->prepare("DELETE FROM maintenance_area_indicators WHERE indicator_code = ?");
    if ($stmt->execute([$indicator_code])) {
        $_SESSION['success'] = "Indicator entry deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete indicator entry.";
    }
    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = "No indicator code specified.";
    header("Location: index.php");
    exit;
}
?>
