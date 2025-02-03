<?php
include 'db.php';

$keyctr = $_GET['keyctr'];

$sql = "SELECT * FROM maintenance_area_mininumreqs WHERE keyctr = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$keyctr]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $sub_mininumreqs = $_POST['sub_mininumreqs'] ?? 0;

    $sql = "UPDATE maintenance_area_mininumreqs SET indicator_keyctr = ?, reqs_code = ?, description = ?, sub_mininumreqs = ? WHERE keyctr = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$indicator_keyctr, $reqs_code, $description, $sub_mininumreqs, $keyctr]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Minimum Requirement</title>
</head>
<body>
    <h1>Edit Minimum Requirement</h1>
    <form action="" method="post">
        <label for="indicator_keyctr">Indicator Keyctr:</label>
        <input type="number" name="indicator_keyctr" value="<?= $req['indicator_keyctr'] ?>" required><br>

        <label for="reqs_code">Reqs Code:</label>
        <input type="text" name="reqs_code" value="<?= $req['reqs_code'] ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required><?= $req['description'] ?></textarea><br>

        <label for="sub_mininumreqs">Sub Minimum Reqs:</label>
        <input type="checkbox" name="sub_mininumreqs" value="1" <?= $req['sub_mininumreqs'] ? 'checked' : '' ?>><br>

        <input type="submit" value="Update">
    </form>
    <a href="index.php">Back to List</a>
</body>
</html>
