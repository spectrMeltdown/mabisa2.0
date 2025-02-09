<?php
session_start();
include '../../db/db.php';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['keyctr']) || empty($_POST['keyctr'])) {
        die("Error: keyctr is missing in POST! Received: " . json_encode($_POST));
    }

    $keyctr = $_POST['keyctr'];
    $description = $_POST['description'];
    $trail = 'Updated at ' . date('Y-m-d H:i:s');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE maintenance_area SET description = :description, trail = :trail WHERE keyctr = :keyctr");
        $stmt->execute(['description' => $description, 'trail' => $trail, 'keyctr' => $keyctr]);

        $pdo->commit();

        $_SESSION['success'] = "Area updated successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header('Location: index.php');
    exit();
}

// Handle the display
if (!isset($_GET['keyctr']) || empty($_GET['keyctr'])) {
    die("Error: keyctr is missing in GET! Received: " . json_encode($_GET));
}

$keyctr = $_GET['keyctr'];

try {
    $stmt = $pdo->prepare("SELECT * FROM maintenance_area WHERE keyctr = :keyctr");
    $stmt->execute(['keyctr' => $keyctr]);
    $area = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$area) {
        die("Error: Area not found!");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>




<div class="modal fade" id="editAreaModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_area.php">
                    <input type="hidden" name="keyctr" value="<?php echo htmlspecialchars($area['keyctr']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"
                            required><?php echo htmlspecialchars($area['description']); ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_area">Update Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>