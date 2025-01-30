<?php
$isBarAss = true;
require_once 'script.php';

$id = $_GET['edit_id'];

try {
    // Get maintenance criteria setup where id = $id
    $stmt = $pdo->prepare("SELECT * FROM `maintenance_criteria_setup` WHERE keyctr = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $maintenance_criteria_setup_row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get maintenance criteria version
    $stmt = $pdo->query("SELECT * FROM `maintenance_criteria_version`");
    $maintenance_criteria_version_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get maintenance area indicators
    $stmt = $pdo->query("SELECT * FROM `maintenance_area_indicators`");
    $maintenance_area_indicators_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get maintenance area minimum requirements
    $stmt = $pdo->query("SELECT * FROM `maintenance_area_mininumreqs`");
    $maintenance_area_mininumreqs_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get maintenance document source
    $stmt = $pdo->query("SELECT * FROM `maintenance_document_source`");
    $maintenance_document_source_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />

    <title>Add</title>
</head>

<body>
    <div class="container">
        <h4>Edit Maintenance Criteria Setup</h4>
        <form action="script.php" method="post">
            <input type="hidden" name="keyctr" value="<?php echo $maintenance_criteria_setup_row['keyctr']; ?>" />
            <br />
            <div class="mb-3">
                <label class="form-label">Version</label>
                <select class="form-control" name="version_keyctr">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_criteria_version_result as $row) { ?>
                        <option <?php echo $maintenance_criteria_setup_row['version_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['short_def']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Indicator</label>
                <select class="form-control" name="indicator_keyctr">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_area_indicators_result as $row) { ?>
                        <option <?php echo $maintenance_criteria_setup_row['indicator_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['indicator_description']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">minreqs_keyctr</label>
                <select class="form-control" name="minreqs_keyctr">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_area_mininumreqs_result as $row) { ?>
                        <option <?php echo $maintenance_criteria_setup_row['minreqs_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['reqs_code'] . " " . $row['description']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">sub_minimumreqs</label>
                <input type="number" class="form-control" name="sub_minimumreqs" value="<?php echo $maintenance_criteria_setup_row['sub_minimumreqs']; ?>" />
            </div>
            <div class="mb-3">
                <label class="form-label">DOCUMENTARY REQUIREMENTS/MOVs</label>
                <textarea class="form-control" name="movdocs_reqs" rows="3"><?php echo trim($maintenance_criteria_setup_row['movdocs_reqs']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Data Source</label>
                <select class="form-control" name="data_source">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_document_source_result as $row) { ?>
                        <option <?php echo $maintenance_criteria_setup_row['data_source'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>"><?php echo $row['srcdesc']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Trail</label>
                <textarea class="form-control" name="trail" rows="3"><?php echo trim($maintenance_criteria_setup_row['trail']); ?></textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary btn-block" name="update_maintenance_criteria_setup">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
