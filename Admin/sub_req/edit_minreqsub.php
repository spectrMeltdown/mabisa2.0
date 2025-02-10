<?php
require '../../db/db.php';
session_start();

if (isset($_GET['keyctr'])) {
    $keyctr = $_GET['keyctr'];

    $stmt = $pdo->prepare("SELECT * FROM maintenance_area_mininumreqs_sub WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $req = $stmt->fetch(PDO::FETCH_ASSOC);

    $mininumreqs = $pdo->query("SELECT keyctr, description FROM maintenance_area_mininumreqs")->fetchAll(PDO::FETCH_ASSOC);
    $indicators = $pdo->query("SELECT keyctr, area_description FROM maintenance_area_indicators")->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyctr = $_POST['keyctr'];
    $mininumreq_keyctr = $_POST['mininumreq_keyctr'];
    $indicator_keyctr = $_POST['indicator_keyctr'];
    $reqs_code = $_POST['reqs_code'];
    $description = $_POST['description'];
    $trail = 'Modified at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE maintenance_area_mininumreqs_sub 
            SET mininumreq_keyctr = :mininumreq_keyctr, 
                indicator_keyctr = :indicator_keyctr, 
                reqs_code = :reqs_code, 
                description = :description, 
                trail = :trail 
            WHERE keyctr = :keyctr");

        $stmt->execute([
            'mininumreq_keyctr' => $mininumreq_keyctr,
            'indicator_keyctr' => $indicator_keyctr,
            'reqs_code' => $reqs_code,
            'description' => $description,
            'trail' => $trail,
            'keyctr' => $keyctr
        ]);

        $pdo->commit();

        $_SESSION['success'] = "Sub Requirement updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating sub requirement: " . $e->getMessage();
    }

    header("Location: index.php");
    exit();
}
?>


<div class="modal fade" id="editMinReqSubModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Sub Requirement</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_minreqsub.php">
                    <input type="hidden" name="keyctr" value="<?= htmlspecialchars($req['keyctr']); ?>">

                    <div class="mb-3">
                        <label class="form-label">Minimum Requirement Code</label>
                        <select class="form-control" name="mininumreq_keyctr" required>
                            <?php foreach ($mininumreqs as $mininumreq): ?>
                                <option value="<?= $mininumreq['keyctr']; ?>" <?= $req['mininumreq_keyctr'] == $mininumreq['keyctr'] ? 'selected' : ''; ?>>
                                    <?=  htmlspecialchars($mininumreq['keyctr'] .'.     ' . $mininumreq['description']);?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Indicator Description</label>
                        <select class="form-control" name="indicator_keyctr" required>
                            <?php foreach ($indicators as $indicator): ?>
                                <option value="<?= $indicator['keyctr']; ?>" <?= $req['indicator_keyctr'] == $indicator['keyctr'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($indicator['area_description']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reqs Code:</label>
                        <input type="text" class="form-control" name="reqs_code" value="<?= htmlspecialchars($req['reqs_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description:</label>
                        <textarea class="form-control" name="description" required><?= htmlspecialchars($req['description']); ?></textarea>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Sub Requirement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


