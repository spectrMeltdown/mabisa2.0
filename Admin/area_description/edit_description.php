<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Fetch the description to edit
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_description WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $description = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Update the description in the database
    $sql = "UPDATE maintenance_area_description SET description = :description, trail = :trail WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['description' => $description, 'trail' => $trail, 'keyctr' => $keyctr]);

    // Set success message and redirect
    $_SESSION['success'] = "Description updated successfully!";
    header('Location: index_description.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Description</title>
</head>
<body>

<h1>Edit Description</h1>

<form method="POST" action="edit_description.php">
    <input type="hidden" name="keyctr" value="<?php echo $description['keyctr']; ?>">

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $description['description']; ?></textarea><br><br>

    <button type="submit">Update Description</button>
</form>

<a href="index_description.php">Back to List</a>

</body>
</html>
