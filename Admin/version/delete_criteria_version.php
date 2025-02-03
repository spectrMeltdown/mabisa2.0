<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Delete the criteria version from the database
    $stmt = $pdo->prepare("DELETE FROM maintenance_criteria_version WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Criteria version deleted successfully!";
    header('Location: index_criteria_version.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
