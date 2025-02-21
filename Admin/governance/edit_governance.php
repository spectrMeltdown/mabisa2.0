<?php
include '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    $stmt = $pdo->prepare("SELECT * FROM maintenance_governance WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $governance = $stmt->fetch(PDO::FETCH_ASSOC);

    $categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);
    $areas = $pdo->query("SELECT * FROM maintenance_area")->fetchAll(PDO::FETCH_ASSOC);
    $descriptions = $pdo->query("SELECT * FROM maintenance_area_description")->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $cat_code = $_POST['cat_code'];
    $area_keyctr = $_POST['area_keyctr'];
    $desc_keyctr = $_POST['desc_keyctr'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();
        $desc_stmt = $pdo->prepare("SELECT description FROM maintenance_area_description WHERE keyctr = ?");
        $desc_stmt->execute([$desc_keyctr]);
        $desc = $desc_stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "UPDATE maintenance_governance SET 
                cat_code = :cat_code, 
                area_keyctr = :area_keyctr, 
                desc_keyctr = :desc_keyctr, 
                description = :description, 
                trail = :trail 
                WHERE keyctr = :keyctr";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cat_code' => $cat_code,
            'area_keyctr' => $area_keyctr,
            'desc_keyctr' => $desc_keyctr,
            'description' => $desc['description'],
            'trail' => $trail,
            'keyctr' => $keyctr
        ]);

        $pdo->commit();
        $log->userLog('Edited a Governance Entry with ID: '.$keyctr.' to Cat Code: '.$cat_code.', Area ID: '.$area_keyctr.', Description ID: '.$desc_keyctr.', and Description: '.$description);
      
        $_SESSION['success'] = "Governance entry updated successfully!";
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
        header('Location: index.php');
        exit();
    }
}
?>


<!-- Edit Governance Modal -->
<div class="modal fade" id="editGovernance" tabindex="-1" aria-labelledby="editGovernanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGovernanceLabel">Edit Governance Entry</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_governance.php">
                    <input type="hidden" name="keyctr" value="<?= htmlspecialchars($governance['keyctr'] ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label">Category Code</label>
                        <select class="form-control" name="cat_code" required>
                            <option value="">Select Category Code</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['code']) ?>"
                                    <?= ($governance['cat_code'] ?? '') == $category['code'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['short_def']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Area Keyctr</label>
                        <select class="form-control" name="area_keyctr" required>
                            <option value="">Select Area</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= htmlspecialchars($area['keyctr']) ?>"
                                    <?= ($governance['area_keyctr'] ?? '') == $area['keyctr'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($area['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description Keyctr</label>
                        <select class="form-control" name="desc_keyctr" required>
                            <option value="">Select Description</option>
                            <?php foreach ($descriptions as $description): ?>
                                <option value="<?= htmlspecialchars($description['keyctr']) ?>"
                                    <?= ($governance['desc_keyctr'] ?? '') == $description['keyctr'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($description['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required><?= htmlspecialchars($governance['description'] ?? '') ?></textarea>
                    </div> -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>