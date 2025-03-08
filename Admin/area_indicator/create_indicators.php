<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

$description_stmt = $pdo->query("SELECT DISTINCT keyctr, description FROM maintenance_area_description");
$descriptions = $description_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_indicator'])) {
    $area_description = $_POST['area_description'];
    $indicator_code = $_POST['indicator_code'];
    $indicator_description = $_POST['indicator_description'];
    $min_requirement = $_POST['min_requirement'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $desc_stmt = $pdo->prepare("SELECT keyctr FROM maintenance_area_description WHERE description = ? LIMIT 1");
        $desc_stmt->execute([$area_description]);
        $desc_data = $desc_stmt->fetch(PDO::FETCH_ASSOC);
        $desc_keyctr = $desc_data ? $desc_data['keyctr'] : null;

        $gov_stmt = $pdo->prepare("SELECT keyctr FROM maintenance_governance WHERE desc_keyctr = ? LIMIT 1");
        $gov_stmt->execute([$desc_keyctr]);
        $gov_data = $gov_stmt->fetch(PDO::FETCH_ASSOC);
        $governance_code = $gov_data ? $gov_data['keyctr'] : null;


        if ($desc_keyctr && $governance_code) {
            $stmt = $pdo->prepare("INSERT INTO maintenance_area_indicators (governance_code, desc_keyctr, area_description, indicator_code, indicator_description, min_requirement, trail) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$governance_code, $desc_keyctr, $area_description, $indicator_code, $indicator_description, $min_requirement, $trail])) {

                $pdo->commit();
                $log->userLog('Created a new Indicator with Governance Code: ' . $governance_code . ', Area Description: ' . $area_description . ', Indicator Description: ' . $indicator_description);
                $_SESSION['success'] = "Indicator entry created successfully!";
            } else {

                $pdo->rollBack();
                $_SESSION['error'] = "Failed to create indicator entry.";
            }
        } else {

            $pdo->rollBack();
            $_SESSION['error'] = "Invalid Area Description selected.";
        }
    } catch (Exception $e) {

        $pdo->rollBack();
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }

    header("Location: index.php");
    exit;
} ?>

<!-- Modal -->
<div class="modal fade" id="addIndicatorModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Add New Indicator Entry</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_indicators.php">
                    <div class="mb-3">
                        <label class="form-label">Area Description</label>
                        <select class="form-control" name="area_description" required>
                            <option value="">Select Area Description</option>
                            <?php foreach ($descriptions as $description): ?>
                                <option value="<?= htmlspecialchars($description['description']) ?>">
                                    <?= htmlspecialchars($description['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicator Code</label>
                        <input type="text" class="form-control" name="indicator_code" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicator Description</label>
                        <input type="text" class="form-control" name="indicator_description" required>
                    </div>

                    <input type="hidden" name="min_requirement" value="1">


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_indicator">Add Indicator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>