<?php
$isInFolder = true;
include '../script.php';
require_once '../../db/db.php';

function fetchAllData($pdo, $query)
{
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$maintenance_criteria_version_result = fetchAllData($pdo, "SELECT * FROM `maintenance_criteria_version`");
$maintenance_area_indicators_result = fetchAllData($pdo, "SELECT * FROM `maintenance_area_indicators`");
$maintenance_area_mininumreqs_result = fetchAllData($pdo, "SELECT * FROM `maintenance_area_mininumreqs`");
$maintenance_document_source_result = fetchAllData($pdo, "SELECT * FROM `maintenance_document_source`");

?>

<!DOCTYPE html>
<html lang="en">

<body>

    <!-- Modal -->
    <div class="modal fade" id="addMaintenanceCriteriaModal" tabindex="-1" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Add Maintenance Criteria Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../script.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Version</label>
                            <select class="form-control" name="version_keyctr">
                                <option value="">Select</option>
                                <?php foreach ($maintenance_criteria_version_result as $row) { ?>
                                    <option value="<?= htmlspecialchars($row['keyctr']) ?>">
                                        <?= htmlspecialchars($row['short_def']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Indicator</label>
                            <select class="form-control" name="indicator_keyctr">
                                <option value="">Select</option>
                                <?php foreach ($maintenance_area_indicators_result as $row) { ?>
                                    <option data-indicator-code="<?= htmlspecialchars($row['indicator_code']) ?>"
                                        value="<?= htmlspecialchars($row['keyctr']) ?>">
                                        <?= htmlspecialchars($row['indicator_code'] . " " . $row['indicator_description']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum Requirements</label>
                            <select class="form-control" name="minreqs_keyctr">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sub Minimum Requirements</label>
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
                                <?php foreach ($maintenance_document_source_result as $row) { ?>
                                    <option value="<?= htmlspecialchars($row['keyctr']) ?>">
                                        <?= htmlspecialchars($row['srcdesc']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trail</label>
                            <textarea class="form-control" name="trail" rows="3"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"
                                name="add_maintenance_criteria_setup">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function () {
            $('select[name="indicator_keyctr"]').change(function () {
                const selectedOption = $(this).find(':selected');
                const indicatorCode = selectedOption.data('indicator-code');

                $.ajax({
                    url: '../script.php',
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>