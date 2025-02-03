<?php
include 'db.php';
session_start();

// Fetch the existing record if an indicator_code is provided
if (isset($_GET['indicator_code'])) {
    $indicator_code = $_GET['indicator_code'];
    
    // Prepare and execute the fetch statement
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_indicators WHERE indicator_code = ?");
    $stmt->execute([$indicator_code]);
    $indicator = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the indicator exists
    if (!$indicator) {
        $_SESSION['error'] = "Indicator entry not found.";
        header("Location: index.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $governance_code = $_POST['governance_code'];
    $desc_keyctr = $_POST['desc_keyctr'];
    $area_description = $_POST['area_description'];
    $indicator_description = $_POST['indicator_description'];
    $relevance_def = $_POST['relevance_def'];
    $min_requirement = isset($_POST['min_requirement']) ? 1 : 0; // Checkbox handling
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    // Prepare and execute the update statement
    $stmt = $pdo->prepare("UPDATE maintenance_area_indicators SET 
        governance_code = ?, 
        desc_keyctr = ?, 
        area_description = ?, 
        indicator_description = ?, 
        relevance_def = ?, 
        min_requirement = ?, 
        trail = ? 
        WHERE indicator_code = ?");
    
    if ($stmt->execute([$governance_code, $desc_keyctr, $area_description, $indicator_description, $relevance_def, $min_requirement, $trail, $indicator_code])) {
        $_SESSION['success'] = "Indicator entry updated successfully!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update indicator entry.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Indicator Entry</title>
</head>
<body>
<h1>Edit Indicator Entry</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style='color: red;'><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<form method="post">
    <label for="governance_code">Governance Code:</label>
    <input type="text" name="governance_code" value="<?php echo htmlspecialchars($indicator['governance_code']); ?>" required>
    <br>

    <label for="desc_keyctr">Description Key:</label>
    <input type="text" name="desc_keyctr" value="<?php echo htmlspecialchars($indicator['desc_keyctr']); ?>" required>
    <br>

    <label for="area_description">Area Description:</label>
    <input type="text" name="area_description" value="<?php echo htmlspecialchars($indicator['area_description']); ?>" required>
    <br>

    <label for="indicator_description">Indicator Description:</label>
    <input type="text" name="indicator_description" value="<?php echo htmlspecialchars($indicator['indicator_description']); ?>" required>
    <br>

    <label for="relevance_def">Relevance Definition:</label>
    <input type="text" name="relevance_def" value="<?php echo htmlspecialchars($indicator['relevance_def']); ?>" required>
    <br>

    <label for="min_requirement">Minimum Requirement:</label>
    <input type="checkbox" name="min_requirement" value="1" <?php echo $indicator['min_requirement'] ? 'checked' : ''; ?> >
    <br>

    <button type="submit">Update Indicator Entry</button>
</form>

<a href="index.php">Back to Indicator List</a>
</body>
</html>
