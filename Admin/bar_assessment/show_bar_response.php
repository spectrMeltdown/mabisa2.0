<?php
require_once './responses.php';
require_once './comments.php';
require_once './admin_actions/admin_actions.php';
require_once '../../db/db.php';

$barangay_id = isset($_GET['barangay_id']) ? $_GET['barangay_id'] : null;
$barangay_name = isset($_GET['brgyname']) ? $_GET['brgyname'] : null;

$responses = new Responses($pdo);

if ($barangay_id) {
    $stmt = $pdo->prepare("SELECT brgyname FROM refbarangay WHERE brgyid = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $barangay = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($barangay) {
        $barangay_name = $barangay['brgyname'];
    } else {
        $barangay_name = 'Unknown';
    }
    $stmt = $pdo->prepare("SELECT * FROM barangay_assessment_files WHERE barangay_id = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $isInFolder = true;
    include '../script.php';
    $data = [];

    $maintenance_area_description_query = "
        SELECT
            maintenance_governance.*,
            maintenance_category.description AS category,
            maintenance_area.description AS area_description
        FROM `maintenance_governance`
        LEFT JOIN maintenance_category ON maintenance_governance.cat_code = maintenance_category.code
        LEFT JOIN maintenance_area ON maintenance_governance.area_keyctr = maintenance_area.keyctr;
    ";

    $stmt = $pdo->prepare($maintenance_area_description_query);
    $stmt->execute();
    $maintenance_area_description_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($maintenance_area_description_result)) {
        foreach ($maintenance_area_description_result as $maintenance_area_description_row) {

            // maintenance_governance
            $maintenance_governance_query = "SELECT * FROM `maintenance_governance` WHERE desc_keyctr = :desc_keyctr";
            $stmt = $pdo->prepare($maintenance_governance_query);
            $stmt->execute(['desc_keyctr' => $maintenance_area_description_row['keyctr']]);
            $maintenance_governance_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($maintenance_governance_result)) {
                foreach ($maintenance_governance_result as $maintenance_governance_row) {

                    // maintenance_area_indicators
                    $maintenance_area_indicators_query = "SELECT * FROM `maintenance_area_indicators` WHERE governance_code = :governance_code";
                    $stmt = $pdo->prepare($maintenance_area_indicators_query);
                    $stmt->execute(['governance_code' => $maintenance_governance_row['keyctr']]);
                    $maintenance_area_indicators_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($maintenance_area_indicators_result)) {
                        foreach ($maintenance_area_indicators_result as $maintenance_area_indicators_row) {

                            // maintenance_criteria_setup
                            $maintenance_criteria_setup_query = "
                                SELECT 
                                    msc.keyctr AS keyctr,
                                    mam.description,
                                    mam.reqs_code,
                                    msc.movdocs_reqs AS documentary_requirements,
                                    mds.srcdesc AS data_source
                                FROM `maintenance_criteria_setup` msc 
                                LEFT JOIN maintenance_criteria_version AS mcv ON msc.version_keyctr = mcv.keyctr
                                LEFT JOIN maintenance_area_mininumreqs AS mam ON msc.minreqs_keyctr = mam.keyctr
                                LEFT JOIN maintenance_document_source AS mds ON msc.data_source = mds.keyctr 
                                WHERE msc.indicator_keyctr = :indicator_keyctr
                                ORDER BY mam.reqs_code ASC
                            ";
                            $stmt = $pdo->prepare($maintenance_criteria_setup_query);
                            $stmt->execute(['indicator_keyctr' => $maintenance_area_indicators_row['keyctr']]);
                            $maintenance_criteria_setup_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($maintenance_criteria_setup_result)) {
                                foreach ($maintenance_criteria_setup_result as $maintenance_criteria_setup_row) {
                                    $data[$maintenance_area_description_row['category'] . " " .
                                        $maintenance_area_description_row['area_description'] . ": " .
                                        $maintenance_area_description_row['description']][] = [
                                        'keyctr' => $maintenance_criteria_setup_row['keyctr'],
                                        'indicator_code' => $maintenance_area_indicators_row['indicator_code'],
                                        'indicator_description' => $maintenance_area_indicators_row['indicator_description'],
                                        'relevance_definition' => $maintenance_area_indicators_row['relevance_def'],
                                        'reqs_code' => $maintenance_criteria_setup_row['reqs_code'],
                                        'documentary_requirements' => $maintenance_criteria_setup_row['documentary_requirements'],
                                        'description' => $maintenance_criteria_setup_row['description'],
                                        'data_source' => $maintenance_criteria_setup_row['data_source'],
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    $data = [];
    $barangay_name = 'Unknown';
}
// echo '<pre>';
// print_r($data);
// echo'</pre>';
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <?php
    $isInFolder = true;
    require '../common/head.php' ?>
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../js/bar-assessment.js"></script>


</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--sidebar start  -->
        <?php
        $isInFolder = true;
        include '../common/sidebar.php'

            ?>

        <!-- sidebar end -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php
                include '../common/nav.php';
                $role = 'Barangay Secretary';
                $name = 'name';
                ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h4>Barangay Details</h4>
                            <p><strong>Barangay ID:</strong> <?php echo htmlspecialchars($barangay_id); ?></p>
                            <p><strong>Barangay Name:</strong> <?php echo htmlspecialchars($barangay_name); ?></p>
                            <a href="../bar_assessment.php" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                    <?php
                    foreach ($data as $key => $rows):
                        // Filter rows to include only those where the role matches the data_source or role is 'admin'
                        $filtered_rows = array_filter($rows, function ($row) use ($role) {
                            return $role === 'Barangay Admin' || $role === $row['data_source'];
                        });

                        // Skip rendering if no matching rows exist
                        if (empty($filtered_rows)) {
                            continue;
                        }
                        ?>
                        <div class="card-header bg-primary text-center py-3">
                            <div class="card-body">
                                <h5 class="text-white"><?php echo htmlspecialchars($key); ?></h5>
                            </div>
                        </div>

                        <?php
                        $last_indicator = '';
                        $table_started = false;

                        foreach ($filtered_rows as $row):
                            $current_indicator = $row['indicator_code'] . " " . $row['indicator_description'];

                            if ($current_indicator !== $last_indicator):

                                if ($table_started) {
                                    echo "</tbody></table>";
                                }
                                ?>

                                <div class="row bg-info" style="margin: 0; padding: 10px 0;">
                                    <h6 class="col-lg-12 text-center text-white" style="margin: 0;">
                                        <?php echo htmlspecialchars($current_indicator); ?>
                                    </h6>
                                </div>

                                <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <?php if ($role === 'Barangay Admin'): ?>
                                                <th style="width: 17%; text-align: center;">Requirement Code</th>
                                                <th style="width: 17%;">Requirement Description</th>
                                                <th style="width: 9%; text-align: center;">Attachment</th>
                                                <th style="width: 6%; text-align: center;">Status</th>
                                                <th style="width: 9%; text-align: center;">Last Modified</th>
                                                <th style="width: 7%; text-align: center;">Approval</th>
                                                <th style="width: 9%; text-align: center;">Comments</th>
                                            <?php elseif ($role === 'Barangay Secretary'): ?>
                                                <th style="width: 17%;">Relevant/Definition</th>
                                                <th style="width: 17%;">Requirement Description</th>
                                                <th style="width: 6%; text-align: center;">Attachment</th>
                                                <th style="width: 4%; text-align: center;">Status</th>
                                                <th style="width: 9%;">Last Modified</th>
                                                <th style="width: 7%; text-align: center;">Comments</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $last_indicator = $current_indicator;
                                        $table_started = true;
                            endif;
                            ?>

                                    <tr>
                                        <td><?php echo $row['relevance_definition']; ?></td>
                                        <td><?php echo $row['reqs_code'] . " " . $row['description']; ?></td>

                                        <?php
                                        $data = $responses->getData($barangay_id, $row['keyctr']);
                                        ?>
                                        <td class="data-cell-upload-view" style="text-align: center; vertical-align: middle;">
                                            <?php if (!$data): ?>
                                                <?php if ($role === 'Barangay Secretary'): ?>
                                                    <form action="../bar_assessment/user_actions/upload.php" method="POST"
                                                        enctype="multipart/form-data" id="uploadForm-<?php echo $row['keyctr']; ?>">
                                                        <input type="hidden" name="barangay_id"
                                                            value="<?php echo htmlspecialchars($barangay_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="criteria_keyctr"
                                                            value="<?php echo htmlspecialchars($row['keyctr'], ENT_QUOTES, 'UTF-8'); ?>">

                                                        <input type="file" name="file" id="file-<?php echo $row['keyctr']; ?>"
                                                            class="file-input" style="display: none;" required>

                                                        <button type="button" class="btn btn-primary" title="Upload"
                                                            onclick="document.getElementById('file-<?php echo $row['keyctr']; ?>').click();">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </form>
                                                <?php elseif ($role === 'Barangay Admin'): ?>
                                                    <p>No Uploads Yet</p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <form method="POST" action="../bar_assessment/admin_actions/view.php"
                                                    target="_blank">
                                                    <input type="hidden" name="file_id"
                                                        value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <button type="submit" class="btn btn-success mb-3" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>

                                        <td class="data-cell-status" style="text-align: center; vertical-align: middle;">
                                            <?php if (!empty($data)): ?>
                                                <?php if ($data['status'] === 'approved'): ?>
                                                    <i class="fa fa-check text-success" title="Approved"></i>
                                                <?php elseif ($data['status'] === 'declined'): ?>
                                                    <i class="fa fa-x text-danger" title="Declined"></i>
                                                <?php else: ?>
                                                    <i class="fa fa-hourglass-start" title="Waiting for Approval"></i>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>

                                        <td class="data-cell-date-uploaded" style="text-align: center; vertical-align: middle;">
                                            <?php echo !empty($data) ? htmlspecialchars($data['date_uploaded']) : ''; ?>
                                        </td>

                                        <?php if ($role === 'Barangay Admin' && $data): ?>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <div class="column">
                                                    <form method="POST" action="admin_actions/change_status.php">
                                                        <input type="hidden" name="file_id"
                                                            value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-success mb-3">Approve</button>
                                                    </form>
                                                    <form method="POST" action="admin_actions/change_status.php">
                                                        <input type="hidden" name="file_id"
                                                            value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="action" value="decline">
                                                        <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                                    </form>
                                                </div>
                                            </td>
                                        <?php endif; ?>

                                        <td class="data-cell-comments" style="text-align: center; vertical-align: middle;">
                                            <?php if ($data): ?>
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#commentModal"
                                                    data-fileid="<?= htmlspecialchars($data['file_id']); ?>"
                                                    data-role="<?= htmlspecialchars($role); ?>"
                                                    data-name="<?= htmlspecialchars($name); ?>">
                                                    Comments
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                                <?php
                                if ($table_started) {
                                    echo "</tbody></table>";
                                }
                                ?>

                            <?php endforeach; ?>

                </div>
            </div>
            <!-- End of Main Content -->
        </div>
    </div>
    </div>
    <?php require '../components/comment_section.php'; ?>
</body>

</html>