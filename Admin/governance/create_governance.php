<?php
include '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

$categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);
$areas = $pdo->query("SELECT * FROM maintenance_area")->fetchAll(PDO::FETCH_ASSOC);
$descriptions = $pdo->query("SELECT * FROM maintenance_area_description")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_code = $_POST['cat_code'];
    $area_keyctr = $_POST['area_keyctr'];
    $desc_keyctr = $_POST['desc_keyctr'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $desc_stmt = $pdo->prepare("SELECT description FROM maintenance_area_description WHERE keyctr = ?");
        $desc_stmt->execute([$desc_keyctr]);
        $desc = $desc_stmt->fetch(PDO::FETCH_ASSOC);


        $sql = "INSERT INTO maintenance_governance (cat_code, area_keyctr, desc_keyctr, description, trail) 
                VALUES (:cat_code, :area_keyctr, :desc_keyctr, :description, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cat_code' => $cat_code,
            'area_keyctr' => $area_keyctr,
            'desc_keyctr' => $desc_keyctr,
            'description' => $desc['description'],
            'trail' => $trail
        ]);

        $pdo->commit();
        $log->userLog('Created a New Governance Entry with Cat Code: '.$cat_code.', Area ID: '.$area_keyctr.', Description ID: '.$desc_keyctr.', and Description: '.$description);
       
        $_SESSION['success'] = "Governance entry created successfully!";
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


<!-- Modal -->
<div class="modal fade" id="addGovernance" tabindex="-1" aria-labelledby="minReqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="minReqModalLabel">Add Minimum Requirement Sub Entry</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_governance.php">
                    <div class="mb-3">
                        <label class="form-label">Category Code</label>
                        <select class="form-control" name="cat_code" required>
                            <option value="">Select Category Code</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['code']) ?>">
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
                                <option value="<?= htmlspecialchars($area['keyctr']) ?>">
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
                                <option value="<?= htmlspecialchars($description['keyctr']) ?>">
                                    <?= htmlspecialchars($description['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div> -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_minreq_sub">Add Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>