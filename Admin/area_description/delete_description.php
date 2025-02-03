<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Delete the description from the database
    $stmt = $pdo->prepare("DELETE FROM maintenance_area_description WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Description deleted successfully!";
    header('Location: index_description.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
