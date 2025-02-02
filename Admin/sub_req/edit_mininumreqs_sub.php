<?php
require 'db.php';

$keyctr = $_GET['keyctr'];

// Fetch values for the foreign key mininumreq_keyctr
$mininumreq_query = $pdo->query("SELECT keyctr, description FROM maintenance_area_mininumreqs");
$mininumreqs = $mininumreq_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch values for the foreign key indicator_keyctr
$indicator_query = $pdo->query("SELECT keyctr, area_description FROM maintenance_area_indicators");
$indicators = $indicator_query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mininumreq_keyctr = $_POST['mininumreq_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $trail = $_POST['trail'];

    $stmt = $pdo->prepare("UPDATE maintenance_area_mininumreqs_sub SET mininumreq_keyctr=?, indicator_keyctr=?, reqs_code=?, description=?, trail=? WHERE keyctr=?");
    $stmt->execute([$mininumreq_keyctr, $indicator_keyctr, $reqs_code, $description, $trail, $keyctr]);

    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM maintenance_area_mininumreqs_sub WHERE keyctr=?");
$stmt->execute([$keyctr]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<form method="POST">
    <!-- Dropdown for mininumreq_keyctr -->
    <label>MininumReq KeyCtr: 
        <select name="mininumreq_keyctr">
            <?php foreach ($mininumreqs as $mininumreq): ?>
                <option value="<?= $mininumreq['keyctr']; ?>" <?= $row['mininumreq_keyctr'] == $mininumreq['keyctr'] ? 'selected' : ''; ?>>
                    <?= $mininumreq['description']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <!-- Dropdown for indicator_keyctr -->
    <label>Indicator KeyCtr: 
        <select name="indicator_keyctr">
            <?php foreach ($indicators as $indicator): ?>
                <option value="<?= $indicator['keyctr']; ?>" <?= $row['indicator_keyctr'] == $indicator['keyctr'] ? 'selected' : ''; ?>>
                    <?= $indicator['area_description']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Reqs Code: <input type="text" name="reqs_code" value="<?= $row['reqs_code']; ?>"></label><br>
    <label>Description: <textarea name="description"><?= $row['description']; ?></textarea></label><br>
    <label>Trail: <textarea name="trail"><?= $row['trail']; ?></textarea></label><br>
    <button type="submit">Update</button>
</form>
