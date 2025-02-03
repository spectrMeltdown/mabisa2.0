<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $active_yr = $_POST['active_yr'];
    $active_ = isset($_POST['active_']) ? 1 : 0;
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    // Insert into the database
    $sql = "INSERT INTO maintenance_criteria_version (short_def, description, active_yr, active_, trail) 
            VALUES (:short_def, :description, :active_yr, :active_, :trail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'short_def' => $short_def,
        'description' => $description,
        'active_yr' => $active_yr,
        'active_' => $active_,
        'trail' => $trail

    ]);

    // Set success message and redirect
    $_SESSION['success'] = "Criteria version created successfully!";
    header('Location: index_criteria_version.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Criteria Version</title>
</head>
<body>

<h1>Add New Criteria Version</h1>

<form method="POST" action="create_criteria_version.php">
    <label>Short Definition:</label><br>
    <input type="text" name="short_def" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Active Year:</label><br>
    <input type="text" name="active_yr" required><br><br>

    <label>Active:</label>
    <input type="checkbox" name="active_" value="1"><br><br>

    <button type="submit">Add Criteria Version</button>
</form>

<a href="index_criteria_version.php">Back to List</a>

</body>
</html>
