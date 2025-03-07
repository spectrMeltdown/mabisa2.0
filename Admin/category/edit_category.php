<?php
include_once '../../db/db.php';
include_once '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $stmt = $pdo->prepare("SELECT * FROM maintenance_category WHERE code = :code");
    $stmt->execute(['code' => $code]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $short_def = $_POST['short_def'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE maintenance_category 
                SET code = :new_code, short_def = :short_def, description = :description, trail = :trail 
                WHERE code = :code";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'new_code' => $code,
            'short_def' => $short_def,
            'description' => $description,
            'trail' => $trail,
            'code' => $_POST['original_code']
        ]);

        $pdo->commit();
        $log->userLog('Edited a Category to Code: ' . $code . ', Short Definiton: ' . $short_def . ', and Description: ' . $description);
        $_SESSION['success'] = "Category updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to update category: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>


<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_category.php">
                    <input type="hidden" name="original_code" value="<?php echo htmlspecialchars($category['code']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" value="<?php echo htmlspecialchars($category['code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Definition</label>
                        <input type="text" class="form-control" name="short_def" value="<?php echo htmlspecialchars($category['short_def']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>