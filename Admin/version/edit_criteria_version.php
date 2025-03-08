<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM maintenance_criteria_version WHERE keyctr = :keyctr");
        $stmt->execute(['keyctr' => $keyctr]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Error fetching criteria version: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $active_yr = $_POST['active_yr'];
    $active_ = isset($_POST['active_']) ? 1 : 0;
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE maintenance_criteria_version SET 
                short_def = :short_def, 
                description = :description, 
                active_yr = :active_yr, 
                active_ = :active_, 
                trail = :trail 
                WHERE keyctr = :keyctr";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'short_def' => $short_def,
            'description' => $description,
            'active_yr' => $active_yr,
            'active_' => $active_,
            'trail' => $trail,
            'keyctr' => $_POST['original_keyctr']
        ]);

        $pdo->commit();
        $log->userLog('Edited a Version with ID:' . $_POST['original_keyctr'] . ' to Short Definition: ' . $short_def . ', Description: ' . $description . ', Active Year: ' . $active_yr . ', and is Active: ' . $active_);
        $_SESSION['success'] = "Criteria version updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating criteria version: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>




<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Criteria</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_criteria_version.php">
                    <input type="hidden" name="original_keyctr" value="<?php echo htmlspecialchars($version['keyctr']); ?>">

                    <div class="mb-3">
                        <label class="form-label">short Definition</label>
                        <input type="text" class="form-control" name="short_def" value="<?php echo htmlspecialchars($version['short_def']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($version['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Active Year</label>
                        <input type="text" class="form-control" name="active_yr" value="<?php echo htmlspecialchars($version['active_yr']); ?>" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="active_" value="1" <?php echo $version['active_'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Active</label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Version</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>