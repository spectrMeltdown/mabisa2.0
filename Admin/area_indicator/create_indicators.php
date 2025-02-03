<?php
include 'db.php';
session_start();

// Fetch governance codes for the dropdown using DISTINCT
$governance_stmt = $pdo->query("SELECT DISTINCT keyctr, cat_code FROM maintenance_governance");
$governance_codes = $governance_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch description keys for the dropdown using DISTINCT
$description_stmt = $pdo->query("SELECT DISTINCT keyctr, description FROM maintenance_area_description");
$description_keys = $description_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $governance_code = $_POST['governance_code']; // This is now the foreign key
    $desc_keyctr = $_POST['desc_keyctr'];
    $area_description = $_POST['area_description'];
    $indicator_code = $_POST['indicator_code'];
    $indicator_description = $_POST['indicator_description'];
    $relevance_def = $_POST['relevance_def'];
    $min_requirement = isset($_POST['min_requirement']) ? 1 : 0;
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    // Insert the new indicator into the database
    $stmt = $pdo->prepare("INSERT INTO maintenance_area_indicators (governance_code, desc_keyctr, area_description, indicator_code, indicator_description, relevance_def, min_requirement, trail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([$governance_code, $desc_keyctr, $area_description, $indicator_code, $indicator_description, $relevance_def, $min_requirement, $trail])) {
        $_SESSION['success'] = "Indicator entry created successfully!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to create indicator entry.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Indicator</title>
</head>
<body>

<h1>Add New Indicator Entry</h1>

<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>{$_SESSION['error']}</p>";
    unset($_SESSION['error']);
}
?>

<form action="" method="post">
    <label for="governance_code">Governance Code:</label>
    <select name="governance_code" id="governance_code" required>
        <option value="">Select Governance Code</option>
        <?php foreach ($governance_codes as $governance): ?>
            <option value="<?= htmlspecialchars($governance['keyctr']) ?>"><?= htmlspecialchars($governance['cat_code']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="desc_keyctr">Description Key:</label>
    <select name="desc_keyctr" id="desc_keyctr" required>
        <option value="">Select Description Key</option>
        <?php foreach ($description_keys as $description): ?>
            <option value="<?= htmlspecialchars($description['keyctr']) ?>"><?= htmlspecialchars($description['description']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="area_description">Area Description:</label>
    <input type="text" name="area_description" id="area_description" required><br><br>

    <label for="indicator_code">Indicator Code:</label>
    <input type="text" name="indicator_code" id="indicator_code" required><br><br>

    <label for="indicator_description">Indicator Description:</label>
    <input type="text" name="indicator_description" id="indicator_description" required><br><br>

    <label for="relevance_def">Relevance Definition:</label>
    <input type="text" name="relevance_def" id="relevance_def" required><br><br>

    <label>Mininum Requirements:</label>
    <input type="checkbox" name="min_requirement" value="1"><br><br>

    <button type="submit">Add Indicator</button>
    <a href="index.php">Cancel</a>
</form>

</body>
</html>
