<?php
require_once '../../db/db.php';
include '../../api/audit_log.php';
$log = new Audit_log($pdo);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $srccode = $_POST['srccode'];
    $srcdesc = $_POST['srcdesc'];
    $trail = 'Created at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO maintenance_document_source (srccode, srcdesc, trail) 
                VALUES (:srccode, :srcdesc, :trail)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'srccode' => $srccode,
            'srcdesc' => $srcdesc,
            'trail' => $trail
        ]);

        $pdo->commit();
        $log->userLog('Created a New Document Source with Source Code: ' . $srccode . ', and Source Description: ' . $srcdesc);
        $_SESSION['success'] = "Document Source created successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating document source: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}
?>


<div class="modal fade" id="addDocuSource" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add New Criteria Version</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="create_document_source.php">
                    <div class="mb-3">
                        <label class="form-label">Source Code</label>
                        <input type="text" class="form-control" name="srccode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source Description</label>
                        <textarea class="form-control" name="srcdesc" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Criteria Version</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>