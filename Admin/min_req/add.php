<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

$query = 'SELECT * FROM `maintenance_area_indicators`';
$stmt = $pdo->prepare($query);
$stmt->execute();
$indicator_id = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_minreq'])) {
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $rel_def = $_POST['relevance'];
    $description = $_POST['description'];
    $sub_mininumreqs = isset($_POST['sub_mininumreqs']) ? 1 : 0;

    try {
        $pdo->beginTransaction();
        $sql = "INSERT INTO maintenance_area_mininumreqs (indicator_keyctr, reqs_code, relevance_definition ,description, sub_mininumreqs) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$indicator_keyctr, $reqs_code, $rel_def, $description, $sub_mininumreqs])) {
            $pdo->commit();
            $log->userLog('Created a new Minimum Requirement with Indicator ID: ' . $indicator_keyctr . ', Requirements Code: ' . $reqs_code . ', Description: ' . $description . ', and Sub Minimum Requirements: ' . $sub_mininumreqs);
            $_SESSION['success'] = "Minimum Requirement created successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to create minimum requirement.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: index.php");
    exit;
}
?>



<div class="modal fade" id="addMinimumReqModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Add New Requirement</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="add.php">

                    <div class="mb-3">
                        <label class="form-label">Indicator Keyctr</label>
                        <select class="form-control" name="indicator_keyctr" required>
                            <option value="">Select</option>
                            <?php foreach ($indicator_id as $row) { ?>
                                <option data-indicator-code="<?= htmlspecialchars($row['indicator_code']) ?>"
                                    value="<?= htmlspecialchars($row['keyctr']) ?>">
                                    <?= htmlspecialchars($row['indicator_code'] . " - " . $row['indicator_description']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relevance Definition</label>
                        <textarea class="form-control" name="relevance" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Requirement Code</label>
                        <input type="text" class="form-control" name="reqs_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <!-- <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="sub_mininumreqs" value="1">
                        <label class="form-check-label">Sub Minimum Requirement</label>
                    </div> -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_minreq">Add Minimum Requirement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>