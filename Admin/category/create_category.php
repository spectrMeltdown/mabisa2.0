<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    // Insert into the database
    $sql = "INSERT INTO maintenance_category (code, short_def, description, trail) VALUES (:code, :short_def, :description, :trail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['code' => $code, 'short_def' => $short_def, 'description' => $description, 'trail' => $trail]);

    // Set success message and redirect
    $_SESSION['success'] = "Category created successfully!";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Category</title>
</head>
<body>

<h1>Add New Category</h1>

<form method="POST" action="create_category.php">
    <label>Code:</label><br>
    <input type="text" name="code" required><br>

    <label>Short Definition:</label><br>
    <input type="text" name="short_def" required><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <button type="submit">Add Category</button>
</form>

<a href="index.php">Back to List</a>

</body>
</html>
