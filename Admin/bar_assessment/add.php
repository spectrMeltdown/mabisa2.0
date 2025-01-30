<?php
$isBarAss = true;
include 'script.php';



// Fetch maintenance criteria version
$maintenance_criteria_version_stmt = $pdo->query("SELECT * FROM `maintenance_criteria_version`");
$maintenance_criteria_versions = $maintenance_criteria_version_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch maintenance area indicators
$maintenance_area_indicators_stmt = $pdo->query("SELECT * FROM `maintenance_area_indicators`");
$maintenance_area_indicators = $maintenance_area_indicators_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch maintenance area minimum requirements
$maintenance_area_minimumreqs_stmt = $pdo->query("SELECT * FROM `maintenance_area_mininumreqs`");
$maintenance_area_minimumreqs = $maintenance_area_minimumreqs_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch maintenance document source
$maintenance_document_source_stmt = $pdo->query("SELECT * FROM `maintenance_document_source`");
$maintenance_document_sources = $maintenance_document_source_stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h4>Add Maintenance Criteria Setup</h4>
        <form action="script.php" method="post">
            <div class="mb-3">
                <label class="form-label">Version</label>
                <select class="form-control" name="version_keyctr">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_criteria_versions as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['keyctr']); ?>">
                            <?php echo htmlspecialchars($row['short_def']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Indicator</label>
                <select class="form-control" name="indicator_keyctr">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_area_indicators as $row): ?>
                        <option data-indicator-code="<?php echo htmlspecialchars($row['indicator_code']); ?>" 
                                value="<?php echo htmlspecialchars($row['keyctr']); ?>">
                            <?php echo htmlspecialchars($row['indicator_code'] . " " . $row['indicator_description']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">minreqs_keyctr</label>
                <select class="form-control" name="minreqs_keyctr"></select>
            </div>
            <div class="mb-3">
                <label class="form-label">sub_minimumreqs</label>
                <input type="number" class="form-control" name="sub_minimumreqs" />
            </div>
            <div class="mb-3">
                <label class="form-label">DOCUMENTARY REQUIREMENTS/MOVs</label>
                <textarea class="form-control" name="movdocs_reqs" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Data Source</label>
                <select class="form-control" name="data_source">
                    <option value="">Select</option>
                    <?php foreach ($maintenance_document_sources as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['keyctr']); ?>">
                            <?php echo htmlspecialchars($row['srcdesc']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Trail</label>
                <textarea class="form-control" name="trail" rows="3"></textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary" name="add_maintenance_criteria_setup">Submit</button>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('select[name="indicator_keyctr"]').change(function () {
                const selectedOption = $(this).find(':selected');
                const indicatorCode = selectedOption.data('indicator-code');
                $.ajax({
                    url: './script.php',
                    type: 'GET',
                    data: { 'indicator_id': indicatorCode },
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.data && Array.isArray(response.data)) {
                            $('select[name="minreqs_keyctr"]').empty().append('<option value="">Select</option>');
                            $.each(response.data, function (index, item) {
                                $('select[name="minreqs_keyctr"]').append(
                                    $('<option>', {
                                        value: item.keyctr,
                                        text: item.reqs_code + " " + item.description
                                    })
                                );
                            });
                        } else {
                            console.error("Unexpected response format:", response);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
