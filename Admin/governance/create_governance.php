<?php
include 'db.php';
session_start();

// Fetching foreign keys data from related tables
$categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);
$areas = $pdo->query("SELECT * FROM maintenance_area")->fetchAll(PDO::FETCH_ASSOC);
$descriptions = $pdo->query("SELECT * FROM maintenance_area_description")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_code = $_POST['cat_code'];
    $area_keyctr = $_POST['area_keyctr'];
    $desc_keyctr = $_POST['desc_keyctr'];
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    // Insert into the database
    $sql = "INSERT INTO maintenance_governance (cat_code, area_keyctr, desc_keyctr, description, trail) 
            VALUES (:cat_code, :area_keyctr, :desc_keyctr, :description, :trail)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cat_code' => $cat_code,
        'area_keyctr' => $area_keyctr,
        'desc_keyctr' => $desc_keyctr,
        'description' => $description,
        'trail' => $trail
    ]);

    // Set success message and redirect
    $_SESSION['success'] = "Governance entry created successfully!";
    header('Location: index_governance.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Governance Entry</title>
</head>
<body>

<h1>Add New Governance Entry</h1>

<form method="POST" action="create_governance.php">
    <label>Category Code:</label><br>
    <select name="cat_code" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['code']; ?>"><?php echo $category['short_def']; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Area Keyctr:</label><br>
    <select name="area_keyctr" required>
        <?php foreach ($areas as $area): ?>
            <option value="<?php echo $area['keyctr']; ?>"><?php echo $area['description']; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description Keyctr:</label><br>
    <select name="desc_keyctr" required>
        <?php foreach ($descriptions as $description): ?>
            <option value="<?php echo $description['keyctr']; ?>"><?php echo $description['description']; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea><br><br>

    <button type="submit">Add Governance Entry</button>
</form>

<a href="index_governance.php">Back to List</a>

</body>
</html>
