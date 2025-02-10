
<?php
include '../../db/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_category (code, short_def, description, trail) 
                VALUES (:code, :short_def, :description, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'code' => $code, 
            'short_def' => $short_def, 
            'description' => $description, 
            'trail' => $trail
        ]);

        $pdo->commit();

        $_SESSION['success'] = "Category created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to create category: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>



<div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add New Category</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_category.php">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Definition</label>
                        <input type="text" class="form-control" name="short_def" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary" name="add_area">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>