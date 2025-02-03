<?php
include 'db.php';
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Fetch the category to edit
    $stmt = $pdo->prepare("SELECT * FROM maintenance_category WHERE code = :code");
    $stmt->execute(['code' => $code]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Update the category in the database
    $sql = "UPDATE maintenance_category SET short_def = :short_def, description = :description, trail = :trail WHERE code = :code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['short_def' => $short_def, 'description' => $description, 'trail' => $trail, 'code' => $code]);

    // Set success message and redirect
    $_SESSION['success'] = "Category updated successfully!";
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
</head>
<body>

<h1>Edit Category</h1>

<form method="POST" action="edit_category.php">
    <input type="hidden" name="code" value="<?php echo $category['code']; ?>">

    <label>Short Definition:</label><br>
    <input type="text" name="short_def" value="<?php echo $category['short_def']; ?>" required><br>

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $category['description']; ?></textarea><br><br>

    <button type="submit">Update Category</button>
</form>

<a href="index.php">Back to List</a>

</body>
</html>
