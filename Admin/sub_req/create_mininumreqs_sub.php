<?php
require 'db.php';

// Check the actual columns for these tables, update accordingly

// Fetch values for the foreign key mininumreq_keyctr (e.g., using `reqs_code` if description does not exist)
$mininumreq_query = $pdo->query("SELECT keyctr, reqs_code FROM maintenance_area_mininumreqs");
$mininumreqs = $mininumreq_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch values for the foreign key indicator_keyctr (assuming there might be a different column to display)
$indicator_query = $pdo->query("SELECT keyctr, area_description FROM maintenance_area_indicators"); // Ensure 'description' exists
$indicators = $indicator_query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mininumreq_keyctr = $_POST['mininumreq_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $trail = $_POST['trail'];

    $stmt = $pdo->prepare("INSERT INTO maintenance_area_mininumreqs_sub (mininumreq_keyctr, indicator_keyctr, reqs_code, description, trail) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$mininumreq_keyctr, $indicator_keyctr, $reqs_code, $description, $trail]);

    header("Location: index.php");
    exit();
}
?>

<form method="POST">
    <!-- Dropdown for mininumreq_keyctr -->
    <label>MininumReq KeyCtr: 
        <select name="mininumreq_keyctr">
            <?php foreach ($mininumreqs as $mininumreq): ?>
                <option value="<?= $mininumreq['keyctr']; ?>"><?= $mininumreq['reqs_code']; ?></option> <!-- Use reqs_code instead -->
            <?php endforeach; ?>
        </select>
    </label><br>

    <!-- Dropdown for indicator_keyctr -->
    <label>Indicator KeyCtr: 
        <select name="indicator_keyctr">
            <?php foreach ($indicators as $indicator): ?>
                <option value="<?= $indicator['keyctr']; ?>"><?= $indicator['area_description']; ?></option> <!-- Ensure description exists -->
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Reqs Code: <input type="text" name="reqs_code"></label><br>
    <label>Description: <textarea name="description"></textarea></label><br>
    <label>Trail: <textarea name="trail"></textarea></label><br>
    <button type="submit">Submit</button>
</form>
