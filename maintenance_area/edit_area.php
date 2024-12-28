<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Fetch the area to edit
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $area = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Update the area in the database
    $sql = "UPDATE maintenance_area SET description = :description, trail = :trail WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['description' => $description, 'trail' => $trail, 'keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Area updated successfully!";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Area</title>
</head>
<body>

<h1>Edit Area</h1>

<form method="POST" action="edit_area.php">
    <input type="hidden" name="keyctr" value="<?php echo $area['keyctr']; ?>">

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $area['description']; ?></textarea><br><br>

    <button type="submit">Update Area</button>
</form>

<a href="index.php">Back to List</a>

</body>
</html>
