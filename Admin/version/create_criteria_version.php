<?php
include '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $active_yr = $_POST['active_yr'];
    $active_ = isset($_POST['active_']) ? 1 : 0;
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_criteria_version (short_def, description, active_yr, active_, trail) 
                VALUES (:short_def, :description, :active_yr, :active_, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'short_def' => $short_def,
            'description' => $description,
            'active_yr' => $active_yr,
            'active_' => $active_,
            'trail' => $trail
        ]);

        $pdo->commit();
        $log->userLog('Created a New Version with Short Definition: '.$short_def.', Description: '.$description.', Active Year: '.$active_yr.', and is Active: '.$active_);
        $_SESSION['success'] = "Criteria version created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to create criteria version: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>

<div class="modal fade" id="addCriteriaVersion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add New Criteria Version</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_criteria_version.php">
                    <div class="mb-3">
                        <label class="form-label">Short Definition</label>
                        <input type="text" class="form-control" name="short_def" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Active Year</label>
                        <input type="text" class="form-control" name="active_yr" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="active_" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Criteria Version</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>