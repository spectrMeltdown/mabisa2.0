<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyctr = $_POST['keyctr'];
    $srccode = $_POST['srccode'];
    $srcdesc = $_POST['srcdesc'];
    $trail = $_POST['trail'];

    $sql = "INSERT INTO maintenance_document_source (keyctr, srccode, srcdesc, trail) VALUES (:keyctr, :srccode, :srcdesc, :trail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyctr' => $keyctr, 'srccode' => $srccode, 'srcdesc' => $srcdesc, 'trail' => $trail]);

    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Document Source</title>
</head>
<body>
    <h1>Add New Document Source</h1>
    <form method="POST">
        <!-- <label>Keyctr:</label>
        <input type="number" name="keyctr" required><br> -->

        <label>Source Code:</label>
        <input type="text" name="srccode" required><br>

        <label>Source Description:</label>
        <textarea name="srcdesc" required></textarea><br>

        <label>Trail:</label>
        <textarea name="trail"></textarea><br>

        <button type="submit">Add</button>
    </form>
</body>
</html>
