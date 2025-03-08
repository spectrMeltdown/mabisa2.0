<?php
session_start();
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_area'])) {
    if (!isset($_POST['keyctr']) || empty($_POST['keyctr'])) {
        die("Error: keyctr is missing in POST!");
    }

    $keyctr = $_POST['keyctr'];
    $area = $_POST['area'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql1 = "UPDATE maintenance_area SET description = :area, trail = :trail WHERE keyctr = :keyctr";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute(['area' => $area, 'trail' => $trail, 'keyctr' => $keyctr]);

        $sql2 = "UPDATE maintenance_area_description SET description = :description WHERE keyctr = :keyctr";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute(['description' => $description, 'keyctr' => $keyctr]);

        $pdo->commit();
        $log->userLog('Edited Area: ' . $area . ' with Description: ' . $description);

        $_SESSION['success'] = "Area updated successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}

// Handle the display
if (!isset($_GET['keyctr']) || empty($_GET['keyctr'])) {
    die("Error: keyctr is missing in GET!");
}

$keyctr = $_GET['keyctr'];

try {
    $stmt = $pdo->prepare("SELECT ma.keyctr, ma.description AS area, mad.description AS description FROM maintenance_area ma JOIN maintenance_area_description mad ON ma.keyctr = mad.keyctr WHERE ma.keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $area = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$area) {
        die("Error: Area not found!");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="modal fade" id="editAreaModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Area</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_area.php">
                    <input type="hidden" name="keyctr" value="<?php echo htmlspecialchars($area['keyctr']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <input type="text" class="form-control" name="area" value="<?php echo htmlspecialchars($area['area']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($area['description']); ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_area">Update Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>