<?php
include 'db.php';
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Delete the category from the database
    $stmt = $pdo->prepare("DELETE FROM maintenance_category WHERE code = :code");
    $stmt->execute(['code' => $code]);

    // Set success message and redirect
    $_SESSION['success'] = "Category deleted successfully!";
    header('Location: index.php');
    exit();
} else {
    echo "Invalid request!";
}
?>
