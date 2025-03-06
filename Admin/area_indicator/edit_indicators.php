<?php
session_start();
require_once '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);

$description_stmt = $pdo->query("SELECT DISTINCT keyctr, description FROM maintenance_area_description");
$descriptions = $description_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_indicator'])) {
    $indicator_code = $_POST['indicator_code'];
    $new_indicator_code = $_POST['new_indicator_code']; // New indicator code
    $area_description = $_POST['area_description'];
    $indicator_description = $_POST['indicator_description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        // Get desc_keyctr
        $desc_stmt = $pdo->prepare("SELECT keyctr FROM maintenance_area_description WHERE description = ? LIMIT 1");
        $desc_stmt->execute([$area_description]);
        $desc_data = $desc_stmt->fetch(PDO::FETCH_ASSOC);
        $desc_keyctr = $desc_data ? $desc_data['keyctr'] : null;

        // Get governance_code
        $gov_stmt = $pdo->prepare("SELECT keyctr FROM maintenance_governance WHERE desc_keyctr = ? LIMIT 1");
        $gov_stmt->execute([$desc_keyctr]);
        $gov_data = $gov_stmt->fetch(PDO::FETCH_ASSOC);
        $governance_code = $gov_data ? $gov_data['keyctr'] : null;

        if ($desc_keyctr && $governance_code) {
            // Check if new indicator code already exists
            if ($new_indicator_code !== $indicator_code) {
                $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM maintenance_area_indicators WHERE indicator_code = ?");
                $check_stmt->execute([$new_indicator_code]);
                if ($check_stmt->fetchColumn() > 0) {
                    throw new Exception("Indicator code already exists. Please choose a different code.");
                }
            }

            // Update indicator details
            $stmt = $pdo->prepare("UPDATE maintenance_area_indicators SET 
                indicator_code = ?, 
                governance_code = ?, 
                desc_keyctr = ?, 
                area_description = ?, 
                indicator_description = ?, 
                trail = ? 
                WHERE indicator_code = ?");

            if ($stmt->execute([$new_indicator_code, $governance_code, $desc_keyctr, $area_description, $indicator_description, $trail, $indicator_code])) {
                $pdo->commit();
                $log->userLog("Updated Indicator: $indicator_code → $new_indicator_code, Governance Code: $governance_code, Area Description: $area_description, Indicator Description: $indicator_description");
                $_SESSION['success'] = "Indicator entry updated successfully!";
                header("Location: index.php");
                exit;
            } else {
                throw new Exception("Failed to update indicator entry.");
            }
        } else {
            throw new Exception("Invalid Area Description selected.");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Fetch existing indicator
if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_indicators WHERE keyctr = ?");
    $stmt->execute([$keyctr]);
    $indicator = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$indicator) {
        die("Error: Indicator not found!");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['indicator_code']) && !isset($indicator)) {
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_indicators WHERE indicator_code = ?");
    $stmt->execute([$_POST['indicator_code']]);
    $indicator = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$indicator) {
        die("Error: Indicator not found during update!");
    }
}
?>

<div class="modal fade" id="editIndicatorModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Indicator</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_indicators.php">
                    <input type="hidden" name="indicator_code" value="<?php echo htmlspecialchars($indicator['indicator_code']); ?>">

                    <div class="mb-3">
                        <label class="form-label">New Indicator Code:</label>
                        <input type="text" class="form-control" name="new_indicator_code" value="<?php echo htmlspecialchars($indicator['indicator_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Area Description</label>
                        <select class="form-control" name="area_description" required>
                            <option value="">Select Area Description</option>
                            <?php foreach ($descriptions as $description): ?>
                                <option value="<?= htmlspecialchars($description['description']) ?>" <?= ($indicator['area_description'] === $description['description']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($description['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicator Description</label>
                        <input type="text" class="form-control" name="indicator_description" value="<?php echo htmlspecialchars($indicator['indicator_description']); ?>" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_indicator" class="btn btn-primary">Update Indicator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
