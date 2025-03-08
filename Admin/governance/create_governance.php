<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

$data = $pdo->query("
   SELECT 
            ma.keyctr AS area_keyctr, 
            ma.description AS area_name, 
            mad.keyctr AS desc_keyctr, 
            mad.description AS description
        FROM maintenance_area AS ma
        JOIN maintenance_area_description AS mad 
            ON ma.keyctr = mad.keyctr")->fetchAll(PDO::FETCH_ASSOC);


$categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
print_r($data);
echo '</pre>';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_code = $_POST['cat_code'];
    $combined_value = $_POST['combined_value'];
    list($area_keyctr, $desc_keyctr) = explode('|', $combined_value);
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_governance (cat_code, area_keyctr, desc_keyctr, trail) 
                VALUES (:cat_code, :area_keyctr, :desc_keyctr, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cat_code' => $cat_code,
            'area_keyctr' => $area_keyctr,
            'desc_keyctr' => $desc_keyctr,
            'trail' => $trail
        ]);

        $pdo->commit();
        $log->userLog('Created a New Governance Entry with Cat Code: ' . $cat_code . ', Area ID: ' . $area_keyctr . ', Description ID: ' . $desc_keyctr);

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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="minReqModalLabel">Add Governance Entry</h5>
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
                        <label class="form-label">Area & Description</label>
                        <select class="form-control" name="combined_value" required>
                            <option value="">Select Area & Description</option>
                            <?php foreach ($data as $entry): ?>
                                <option value="<?= htmlspecialchars($entry['area_keyctr'] . '|' . $entry['desc_keyctr']) ?>">
                                    <?= htmlspecialchars($entry['area_name'] . ' - ' . $entry['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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