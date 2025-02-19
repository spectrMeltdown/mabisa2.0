<?php
require '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

$keyctr = $_GET['keyctr'];

try {
    $stmt = $pdo->prepare("SELECT * FROM maintenance_document_source WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $document_source = $stmt->fetch();
} catch (Exception $e) {
    die("Error fetching document source: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $srccode = $_POST['srccode'];
    $srcdesc = $_POST['srcdesc'];
    $trail = 'Modified at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE maintenance_document_source 
                SET srccode = :srccode, srcdesc = :srcdesc, trail = :trail 
                WHERE keyctr = :keyctr";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'srccode' => $srccode,
            'srcdesc' => $srcdesc,
            'trail' => $trail,
            'keyctr' => $_POST['original_keyctr']
        ]);

        $pdo->commit();
        $log->userLog('Edited a Document Source with ID:'.$_POST['original_keyctr'] . ' to Source Code: '.$srccode.', and Source Description: '.$srcdesc);
       
        $_SESSION['success'] = "Document Source modified successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error modifying document source: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>



<div class="modal fade" id="editDocuSourceModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_document_source.php">
                    <input type="hidden" name="original_keyctr" value="<?php echo htmlspecialchars($document_source['keyctr']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Source Code</label>
                        <input type="text" class="form-control" name="srccode" value="<?php echo htmlspecialchars($document_source['srccode']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Source Description</label>
                        <textarea class="form-control" name="srcdesc" required><?php echo htmlspecialchars($document_source['srcdesc']); ?></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Version</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>