<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_area_description (description, trail) VALUES (:description, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['description' => $description, 'trail' => $trail]);

        $pdo->commit();

        $log->userLog('Created a new Area Description: ' . $description);
        $_SESSION['success'] = "Description created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating description: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>

<div class="modal fade" id="addAreaDescriptionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Add New Description</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="add_description.php">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary" name="add_area">Add Description</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>