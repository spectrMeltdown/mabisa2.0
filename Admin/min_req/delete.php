<?php
include 'db.php';

$keyctr = $_GET['keyctr'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "DELETE FROM maintenance_area_mininumreqs WHERE keyctr = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$keyctr]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Minimum Requirement</title>
</head>
<body>
    <h1>Delete Minimum Requirement</h1>
    <p>Are you sure you want to delete this requirement?</p>
    <form action="" method="post">
        <input type="submit" value="Delete">
    </form>
    <a href="index.php">Cancel</a>
</body>
</html>
