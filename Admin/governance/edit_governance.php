<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    // Fetch the existing governance entry
    $stmt = $pdo->prepare("SELECT * FROM maintenance_governance WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $governance = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch categories
    $categories = $pdo->query("SELECT * FROM maintenance_category")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch combined area & description values
    $data = $pdo->query("
        SELECT 
            ma.keyctr AS area_keyctr, 
            ma.description AS area_name, 
            mad.keyctr AS desc_keyctr, 
            mad.description AS description
        FROM maintenance_area AS ma
        JOIN maintenance_area_description AS mad 
            ON ma.keyctr = mad.keyctr
    ")->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $cat_code = $_POST['cat_code'];
    $combined_value = $_POST['combined_value'];
    list($area_keyctr, $desc_keyctr) = explode('|', $combined_value);
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        // Update governance entry
        $sql = "UPDATE maintenance_governance SET 
                cat_code = :cat_code, 
                area_keyctr = :area_keyctr, 
                desc_keyctr = :desc_keyctr, 
                trail = :trail 
                WHERE keyctr = :keyctr";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cat_code' => $cat_code,
            'area_keyctr' => $area_keyctr,
            'desc_keyctr' => $desc_keyctr,
            'trail' => $trail,
            'keyctr' => $keyctr
        ]);

        $pdo->commit();
        $log->userLog('Updated Governance Entry with ID: ' . $keyctr . ' to Cat Code: ' . $cat_code . ', Area ID: ' . $area_keyctr . ', Description ID: ' . $desc_keyctr);

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
                        <label class="form-label">Area & Description</label>
                        <select class="form-control" name="combined_value" required>
                            <option value="">Select Area & Description</option>
                            <?php foreach ($data as $entry): ?>
                                <option value="<?= htmlspecialchars($entry['area_keyctr'] . '|' . $entry['desc_keyctr']) ?>"
                                    <?= ($governance['area_keyctr'] . '|' . $governance['desc_keyctr']) == ($entry['area_keyctr'] . '|' . $entry['desc_keyctr']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($entry['area_name'] . ' - ' . $entry['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>