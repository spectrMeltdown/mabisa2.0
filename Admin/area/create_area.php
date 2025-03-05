<?php
include '../../db/db.php';
include '../../api/audit_log.php';
session_start();

$log = new Audit_log($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_area'])) {
    $area = $_POST['area'];
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql1 = "INSERT INTO maintenance_area (description, trail) VALUES (:area, :trail)";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute(['area' => $area, 'trail' => $trail]);

        $keyctr = $pdo->lastInsertId();

        $sql2 = "INSERT INTO maintenance_area_description (keyctr, description) VALUES (:keyctr, :description)";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute(['keyctr' => $keyctr, 'description' => $description]);

        $pdo->commit();
        $log->userLog('Created a New Area: ' . $area . ' with Description: ' . $description);

        $_SESSION['success'] = "Area created successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>



<div class="modal fade" id="addAreaModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add New Area</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_area.php">
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <input type="text" class="form-control" name="area" required></input>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary" name="add_area">Add Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>