<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    // Insert into the database
    $sql = "INSERT INTO maintenance_area_description (description, trail) VALUES (:description, :trail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['description' => $description, 'trail' => $trail]);

    // Set success message and redirect
    $_SESSION['success'] = "Description created successfully!";
    header('Location: index_description.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Description</title>
</head>
<body>

<h1>Add New Description</h1>

<form method="POST" action="create_description.php">
    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <button type="submit">Add Description</button>
</form>

<a href="index_description.php">Back to List</a>

</body>
</html>
