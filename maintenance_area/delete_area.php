<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Delete the area from the database
    $stmt = $pdo->prepare("DELETE FROM maintenance_area WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Area deleted successfully!";
    header('Location: index.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
