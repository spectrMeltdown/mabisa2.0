<?php
include 'db.php';
session_start();

$categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);
$areas = $pdo->query("SELECT * FROM maintenance_area")->fetchAll(PDO::FETCH_ASSOC);
$descriptions = $pdo->query("SELECT * FROM maintenance_area_description")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Fetch the governance entry to edit
    $stmt = $pdo->prepare("SELECT * FROM maintenance_governance WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $governance = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $cat_code = $_POST['cat_code'];
    $area_keyctr = $_POST['area_keyctr'];
    $desc_keyctr = $_POST['desc_keyctr'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Update the governance entry in the database
    $sql = "UPDATE maintenance_governance SET 
            cat_code = :cat_code, 
            area_keyctr = :area_keyctr, 
            desc_keyctr = :desc_keyctr, 
            description = :description, 
            trail = :trail 
            WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cat_code' => $cat_code,
        'area_keyctr' => $area_keyctr,
        'desc_keyctr' => $desc_keyctr,
        'description' => $description,
        'trail' => $trail,
        'keyctr' => $keyctr
    ]);

    // Set success message and redirect
    $_SESSION['success'] = "Governance entry updated successfully!";
    header('Location: index_governance.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Governance Entry</title>
</head>
<body>

<h1>Edit Governance Entry</h1>

<form method="POST" action="edit_governance.php">
    <input type="hidden" name="keyctr" value="<?php echo $governance['keyctr']; ?>">

    <label>Category Code:</label><br>
    <select name="cat_code" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['code']; ?>" <?php echo ($governance['cat_code'] == $category['code']) ? 'selected' : ''; ?>>
                <?php echo $category['short_def']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Area Keyctr:</label><br>
    <select name="area_keyctr" required>
        <?php foreach ($areas as $area): ?>
            <option value="<?php echo $area['keyctr']; ?>" <?php echo ($governance['area_keyctr'] == $area['keyctr']) ? 'selected' : ''; ?>>
                <?php echo $area['description']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description Keyctr:</label><br>
    <select name="desc_keyctr" required>
        <?php foreach ($descriptions as $description): ?>
            <option value="<?php echo $description['keyctr']; ?>" <?php echo ($governance['desc_keyctr'] == $description['keyctr']) ? 'selected' : ''; ?>>
                <?php echo $description['description']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $governance['description']; ?></textarea><br><br>

    <button type="submit">Update Governance Entry</button>
</form>

<a href="index_governance.php">Back to List</a>

</body>
</html>
