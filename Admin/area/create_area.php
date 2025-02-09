<?php
include '../../db/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_area'])) {
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_area (description, trail) VALUES (:description, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['description' => $description, 'trail' => $trail]);

        $pdo->commit();

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