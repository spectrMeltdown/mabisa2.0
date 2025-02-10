<?php
include '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_mininumreqs WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $req = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyctr = $_POST['keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $sub_mininumreqs = isset($_POST['sub_mininumreqs']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        $sql = "UPDATE maintenance_area_mininumreqs 
                SET indicator_keyctr = :indicator_keyctr, 
                    reqs_code = :reqs_code, 
                    description = :description, 
                    sub_mininumreqs = :sub_mininumreqs 
                WHERE keyctr = :keyctr";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'indicator_keyctr' => $indicator_keyctr,
            'reqs_code' => $reqs_code,
            'description' => $description,
            'sub_mininumreqs' => $sub_mininumreqs,
            'keyctr' => $keyctr
        ]);

        $pdo->commit();

        $_SESSION['success'] = "Minimum requirement updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating minimum requirement: " . $e->getMessage();
    }

    header("Location: index.php");
    exit();
}
?>


<div class="modal fade" id="editMinimumReqModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Minimum Requirement</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit.php">
                    <input type="hidden" name="keyctr" value="<?php echo htmlspecialchars($req['keyctr']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Indicator Keyctr:</label>
                        <input type="number" class="form-control" name="indicator_keyctr" value="<?php echo htmlspecialchars($req['indicator_keyctr']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reqs Code:</label>
                        <input type="text" class="form-control" name="reqs_code" value="<?php echo htmlspecialchars($req['reqs_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description:</label>
                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($req['description']); ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="sub_mininumreqs" value="1" <?php echo $req['sub_mininumreqs'] ? 'checked' : ''; ?>>
                        <label class="form-check-label">Sub Minimum Reqs</label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Requirement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
