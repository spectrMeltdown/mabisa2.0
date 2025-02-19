<?php
require '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

// Fetch values for foreign key mininumreq_keyctr
$mininumreq_query = $pdo->query("SELECT keyctr, reqs_code, description FROM maintenance_area_mininumreqs");
$mininumreqs = $mininumreq_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch values for foreign key indicator_keyctr
$indicator_query = $pdo->query("SELECT keyctr, area_description FROM maintenance_area_indicators");
$indicators = $indicator_query->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_minreq_sub'])) {
    $mininumreq_keyctr = $_POST['mininumreq_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO maintenance_area_mininumreqs_sub (mininumreq_keyctr, indicator_keyctr, reqs_code, description, trail) VALUES (?, ?, ?, ?, ?)");

        if ($stmt->execute([$mininumreq_keyctr, $indicator_keyctr, $reqs_code, $description, $trail])) {
            $pdo->commit();
            $log->userLog('Created a New Minimum Requirement Sub Entry with Minimum Requirement Keyctr: ' . $mininumreq_keyctr . ', Indicator Keyctr: ' . $indicator_keyctr . ', Requirements Code: ' . $reqs_code . ', and Description: ' . $description);
            $_SESSION['success'] = "Minimum Requirement Sub entry created successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = "Failed to create Minimum Requirement Sub entry.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }

    header("Location: index.php");
    exit();
}
?>

<!-- Modal -->
<div class="modal fade" id="addMinReqSubModal" tabindex="-1" aria-labelledby="minReqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="minReqModalLabel">Add Minimum Requirement Sub Entry</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="add_minreqsub.php">
                    <div class="mb-3">
                        <label class="form-label">Minimum Requirement Code</label>
                        <select class="form-control" name="mininumreq_keyctr" required>
                            <option value="">Select Minimum Requirement</option>
                            <?php foreach ($mininumreqs as $mininumreq): ?>
                                <option value="<?= htmlspecialchars($mininumreq['keyctr']) ?>">
                                    <?= htmlspecialchars($mininumreq['keyctr'] . '.     ' . $mininumreq['description']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicator Description</label>
                        <select class="form-control" name="indicator_keyctr" required>
                            <option value="">Select Indicator</option>
                            <?php foreach ($indicators as $indicator): ?>
                                <option value="<?= htmlspecialchars($indicator['keyctr']) ?>">
                                    <?= htmlspecialchars($indicator['area_description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reqs Code</label>
                        <input type="text" class="form-control" name="reqs_code" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_minreq_sub">Add Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>