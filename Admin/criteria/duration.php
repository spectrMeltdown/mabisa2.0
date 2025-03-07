<?php
$isInFolder = true;
include_once '../script.php';
require_once '../../db/db.php';

function fetchAllData($pdo, $query)
{
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$active_version = fetchAllData($pdo, "SELECT * FROM `maintenance_criteria_version` WHERE active_ = 1");

if (!empty($active_version)) {
    $active_version = $active_version[0];
} else {
    $active_version = [];
}

echo '<pre>';
print_r($active_version);
echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">

<body>

    <!-- Modal -->
    <div class="modal fade" id="addDurationModal" tabindex="-1" aria-labelledby="modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Add Maintenance Criteria Setup</h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <form action="../script.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Assessment Duration</label>
                            <input type="text" class="form-control" name="duration" value="<?php echo $active_version['duration']; ?>" required />

                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_accepting_response" value="1"
                                <?php echo !empty($active_version['is_accepting_response']) && $active_version['is_accepting_response'] ? 'checked' : ''; ?>>

                            <label class="form-check-label">End of Assessment</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"
                                name="edit_duration">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>





</body>


</html>