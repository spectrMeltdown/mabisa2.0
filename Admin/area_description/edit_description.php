<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM maintenance_area_description WHERE keyctr = :keyctr");
        $stmt->execute(['keyctr' => $keyctr]);
        $description = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Error fetching description: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keyctr = $_POST['keyctr'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE maintenance_area_description SET description = :description, trail = :trail WHERE keyctr = :keyctr";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['description' => $description, 'trail' => $trail, 'keyctr' => $keyctr]);

        $pdo->commit();

        $_SESSION['success'] = "Description updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating description: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>


<div class="modal fade" id="editAreaDescriptionModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Description</h5>
               
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_description.php">
                    <input type="hidden" name="keyctr" value="<?php echo htmlspecialchars($description['keyctr']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"
                            required><?php echo htmlspecialchars( $description['description']); ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_area">Update Description</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
