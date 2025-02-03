<?php
require 'db.php';

$keyctr = $_GET['keyctr'];
$sql = "SELECT * FROM maintenance_document_source WHERE keyctr = :keyctr";
$stmt = $pdo->prepare($sql);
$stmt->execute(['keyctr' => $keyctr]);
$document_source = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $srccode = $_POST['srccode'];
    $srcdesc = $_POST['srcdesc'];
    $trail = $_POST['trail'];

    $sql = "UPDATE maintenance_document_source SET srccode = :srccode, srcdesc = :srcdesc, trail = :trail WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['srccode' => $srccode, 'srcdesc' => $srcdesc, 'trail' => $trail, 'keyctr' => $keyctr]);

    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Document Source</title>
</head>
<body>
    <h1>Edit Document Source</h1>
    <form method="POST">
        <label>Keyctr:</label>
        <input type="number" name="keyctr" value="<?= htmlspecialchars($document_source['keyctr']); ?>" readonly><br>

        <label>Source Code:</label>
        <input type="text" name="srccode" value="<?= htmlspecialchars($document_source['srccode']); ?>" required><br>

        <label>Source Description:</label>
        <textarea name="srcdesc" required><?= htmlspecialchars($document_source['srcdesc']); ?></textarea><br>

        <label>Trail:</label>
        <textarea name="trail"><?= htmlspecialchars($document_source['trail']); ?></textarea><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
