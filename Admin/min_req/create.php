<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $sub_mininumreqs = $_POST['sub_mininumreqs'];

    $sql = "INSERT INTO maintenance_area_mininumreqs (indicator_keyctr, reqs_code, description, sub_mininumreqs) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$indicator_keyctr, $reqs_code, $description, $sub_mininumreqs]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Minimum Requirement</title>
</head>
<body>
    <h1>Create Minimum Requirement</h1>
    <form action="" method="post">
        <label for="indicator_keyctr">Indicator Keyctr:</label>
        <input type="number" name="indicator_keyctr" required><br>

        <label for="reqs_code">Reqs Code:</label>
        <input type="text" name="reqs_code" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="sub_mininumreqs">Sub Minimum Reqs:</label>
        <input type="checkbox" name="sub_mininumreqs" value="1"><br>

        <input type="submit" value="Create">
    </form>
    <a href="index.php">Back to List</a>
</body>
</html>
