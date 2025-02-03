<?php
include 'db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Fetch the criteria version to edit
    $stmt = $pdo->prepare("SELECT * FROM maintenance_criteria_version WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $criteria_version = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $active_yr = $_POST['active_yr'];
    $active_ = isset($_POST['active_']) ? 1 : 0;
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Update the criteria version in the database
    $sql = "UPDATE maintenance_criteria_version SET 
            short_def = :short_def, 
            description = :description, 
            active_yr = :active_yr, 
            active_ = :active_, 
            trail = :trail 
            WHERE keyctr = :keyctr";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'short_def' => $short_def,
        'description' => $description,
        'active_yr' => $active_yr,
        'active_' => $active_,
        'trail' => $trail,
        'keyctr' => $keyctr
    ]);

    // Set success message and redirect
    $_SESSION['success'] = "Criteria version updated successfully!";
    header('Location: index_criteria_version.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Criteria Version</title>
</head>
<body>

<h1>Edit Criteria Version</h1>

<form method="POST" action="edit_criteria_version.php">
    <input type="hidden" name="keyctr" value="<?php echo $criteria_version['keyctr']; ?>">

    <label>Short Definition:</label><br>
    <input type="text" name="short_def" value="<?php echo $criteria_version['short_def']; ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $criteria_version['description']; ?></textarea><br><br>

    <label>Active Year:</label><br>
    <input type="text" name="active_yr" value="<?php echo $criteria_version['active_yr']; ?>" required><br><br>

    <label>Active:</label>
    <input type="checkbox" name="active_" value="1" <?php echo $criteria_version['active_'] ? 'checked' : ''; ?>><br><br>

    <button type="submit">Update Criteria Version</button>
</form>

<a href="index_criteria_version.php">Back to List</a>

</body>
</html>
