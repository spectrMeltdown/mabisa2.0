<?php
$isInFolder = true;
require_once '../script.php';
$id = isset($_POST['edit_id']) ? trim($_POST['edit_id']) : null;

if (!$id) {
    die("Error: No valid ID received.");
}
try {
    $stmt = $pdo->prepare("SELECT * FROM `maintenance_criteria_setup` WHERE keyctr = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $maintenance_criteria_setup_row = $stmt->fetch(PDO::FETCH_ASSOC);

    $maintenance_criteria_version_result = $pdo->query("SELECT * FROM `maintenance_criteria_version`")->fetchAll(PDO::FETCH_ASSOC);
    $maintenance_area_indicators_result = $pdo->query("SELECT * FROM `maintenance_area_indicators`")->fetchAll(PDO::FETCH_ASSOC);
    $maintenance_area_mininumreqs_result = $pdo->query("SELECT * FROM `maintenance_area_mininumreqs`")->fetchAll(PDO::FETCH_ASSOC);
    $maintenance_document_source_result = $pdo->query("SELECT * FROM `maintenance_document_source`")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>


<div class="modal fade" id="displayIdModal" tabindex="-1" aria-labelledby="displayIdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="displayIdModalLabel">Display ID</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button> -->
            </div>
            <div class="modal-body">
                <form action="../script.php" method="post">
                <input type="hidden" id="modalEditId" name="edit_id">

                    <input type="hidden" name="keyctr" value="<?php echo $maintenance_criteria_setup_row['keyctr']; ?>" />
                    
                    <div class="mb-3">
                        <label class="form-label">Version</label>
                        <select class="form-control" name="version_keyctr">
                            <option value="">Select</option>
                            <?php foreach ($maintenance_criteria_version_result as $row) { ?>
                                <option <?php echo $maintenance_criteria_setup_row['version_keyctr'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>">
                                    <?php echo $row['short_def']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Indicator</label>
                        <select class="form-control" name="indicator_keyctr" id="indicatorSelect">
                            <option value="">Select</option>
                            <?php foreach ($maintenance_area_indicators_result as $row) { ?>
                                <option 
                                    data-indicator-code="<?= htmlspecialchars($row['indicator_code']) ?>"
                                    value="<?= htmlspecialchars($row['keyctr']) ?>"
                                    <?= ($maintenance_criteria_setup_row['indicator_keyctr'] == $row['keyctr']) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($row['indicator_description']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Minimum Requirements</label>
                        <select class="form-control" name="minreqs_keyctr" id="minreqsSelect">
                            <option value="">Select</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sub Minimum Requirements</label>
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
                                <option <?php echo $maintenance_criteria_setup_row['data_source'] == $row['keyctr'] ? "selected" : "" ?> value="<?php echo $row['keyctr']; ?>">
                                    <?php echo $row['srcdesc']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trail</label>
                        <textarea class="form-control" name="trail" rows="3"><?php echo trim($maintenance_criteria_setup_row['trail']); ?></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_maintenance_criteria_setup">Submit</button>
                    </div>
                </form>
            </div>
           
        </div>
    </div>
</div>

<!-- Trigger Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var displayIdModal = new bootstrap.Modal(document.getElementById('displayIdModal'));
        displayIdModal.show();

    });
    $(document).ready(function () {
        function loadMinimumRequirements(indicatorCode, selectedMinReq) {
            $.ajax({
                url: '../script.php',
                type: 'GET',
                data: { 'indicator_id': indicatorCode },
                dataType: 'JSON',
                success: function (response) {
                    let minReqDropdown = $('#minreqsSelect');
                    minReqDropdown.empty().append('<option value="">Select</option>');
                    if (response.data && Array.isArray(response.data)) {
                        $.each(response.data, function (index, item) {
                            let option = $('<option>', {
                                value: item.keyctr,
                                text: item.reqs_code + " " + item.description,
                            });
                            if (selectedMinReq == item.keyctr) {
                                option.attr("selected", "selected");
                            }
                            minReqDropdown.append(option);
                        });
                    }
                }
            });
        }

        let selectedIndicator = $('#indicatorSelect').find(":selected").data("indicator-code");
        let selectedMinReq = "<?= htmlspecialchars($maintenance_criteria_setup_row['minreqs_keyctr']) ?>";
        if (selectedIndicator) {
            loadMinimumRequirements(selectedIndicator, selectedMinReq);
        }
        
        $('#indicatorSelect').change(function () {
            let newIndicatorCode = $(this).find(':selected').data('indicator-code');
            loadMinimumRequirements(newIndicatorCode, '');
        });
    });
</script>
