<?php
$pathPrepend = '../../';
require_once './responses.php';
require_once './comments.php';
require_once './admin_actions/admin_actions.php';
require_once '../../db/db.php';

$barangay_id = isset($_GET['barangay_id']) ? $_GET['barangay_id'] : null;
$barangay_name = isset($_GET['brgyname']) ? $_GET['brgyname'] : null;

$responses = new Responses($pdo);
$admin = new Admin_Actions($pdo);

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

    include 'script.php';
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
    $isBarAss = true;
    require '../common/head.php' ?>
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../js/bar-assessment.js"></script>


</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--sidebar start  -->
        <?php
        $isBarAss = true;
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
                $role = $userData['role'];
                $name = $userData['fullName'];


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
                    $last_indicator = '';
                    foreach ($data as $key => $rows): ?>
                        <div class="card-header bg-primary text-center py-3">
                            <div class="card-body">
                                <h5 class="text-white"><?php echo htmlspecialchars($key); ?></h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM maintenance_area_indicators WHERE desc_keyctr = ?");
                                $stmt->bindParam(1, $area_desc['keyctr'], PDO::PARAM_INT);
                                $stmt->execute();
                                $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if (!empty($indicators)): ?>
                                    <?php foreach ($indicators as $indicator): ?>
                                        <div class="row bg-info">
                                            <h6 class="col-lg-12 text-center text-white">
                                                <?php echo htmlspecialchars($indicator['indicator_code'] . ' ' . $indicator['indicator_description']); ?>
                                            </h6>
                                        </div>

                                        <!-- Query for maintenance_area_mininumreqs -->
                                        <?php
                                        $minReqStmt = $pdo->prepare("SELECT * FROM maintenance_area_mininumreqs WHERE indicator_code = ?");
                                        $minReqStmt->bindParam(1, $indicator['indicator_code'], PDO::PARAM_STR);
                                        $minReqStmt->execute();
                                        $minRequirements = $minReqStmt->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if (!empty($minRequirements)): ?>
                                            <div class="row mt-3">
                                                <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                                                    <?php if ($role === 'Admin'): ?>
                                                        <thead class="bg-secondary text-white">
                                                            <tr>
                                                                <th style="width: 9%; text-align: center;">Requirement Code</th>
                                                                <th style="width: 25%;">Requirement Description</th>
                                                                <th style="width: 9%;text-align: center;">Attachment</th>
                                                                <th style="width: 6%;text-align: center;">Status</th>
                                                                <th style="width: 10%; text-align: center;"> Last Modified</th>
                                                                <th style="width: 7%;text-align: center;"> Approval</th>
                                                                <th style="width: 9%;text-align: center;"> Comments</th>
                                                            </tr>
                                                        </thead>
                                                    <?php endif; ?>
                                                    <?php if ($role === 'Secretary'): ?>
                                                        <thead class="bg-secondary text-white">
                                                            <tr>
                                                                <th style="width: 9%;">Requirement Code</th>
                                                                <th style="width: 25%;">Requirement Description</th>
                                                                <th style="width: 6%; text-align: center;">Attachment</th>
                                                                <th style="width: 4%; text-align: center;">Status</th>
                                                                <th style="width: 9%;"> Last Modified</th>
                                                                <th style="width: 7%; text-align: center;"> Comments</th>
                                                            </tr>
                                                        </thead>
                                                    <?php endif; ?>
                                                    <tbody>
                                                        <?php foreach ($minRequirements as $minReq):
                                                            $current_req_keyctr = $minReq['keyctr']; ?>

                                                            <tr>
                                                                <td style="text-align: center; vertical-align: middle;">
                                                                    <?php echo htmlspecialchars($minReq['reqs_code']); ?>
                                                                </td>
                                                                <td style="vertical-align: middle;">
                                                                    <?php echo nl2br(htmlspecialchars($minReq['description'])); ?>
                                                                </td>
                                                                <td class="data-cell-upload-view"
                                                                    style="text-align: center; vertical-align: middle;"> <?php
                                                                                                                            $data = $responses->getData($barangay_id, $minReq['keyctr'], $area_desc['keyctr'], $indicator['indicator_code'], $minReq['reqs_code']);

                                                                                                                            if (!$data): ?>

                                                                        <!-- Case: Secretary Role -->
                                                                        <?php if ($role === 'Secretary'): ?>
                                                                            <form method="POST" action="../bar_assessment/user_actions/upload.php"
                                                                                enctype="multipart/form-data"
                                                                                id="uploadForm_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="barangay_id"
                                                                                    value="<?php echo htmlspecialchars($barangay_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="req_keyctr"
                                                                                    value="<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="desc_ctr"
                                                                                    value="<?php echo htmlspecialchars($area_desc['keyctr'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="indicator_code"
                                                                                    value="<?php echo htmlspecialchars($indicator['indicator_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="hidden" name="reqs_code"
                                                                                    value="<?php echo htmlspecialchars($minReq['reqs_code'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <input type="file" name="file"
                                                                                    id="fileInput_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>"
                                                                                    style="display: none;" required>
                                                                                <button type="button" class="btn btn-primary" title="Upload"
                                                                                    onclick="document.getElementById('fileInput_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>').click();">
                                                                                    <i class="fa fa-upload"></i>
                                                                                </button>
                                                                            </form>
                        </div>

                        <?php foreach ($rows as $row):
                            $current_row = $row['keyctr'];
                            ?>
                            <?php
                            $current_indicator = $row['indicator_code'] . " " . $row['indicator_description'];

                            if ($current_indicator !== $last_indicator):
                                ?>
                                <div class="row bg-info" style="margin: 0; padding: 10px 0;">
                                    <h6 class="col-lg-12 text-center text-white" style="margin: 0;">
                                        <?php echo htmlspecialchars($current_indicator); ?>
                                    </h6>
                                </div>
                                <?php
                                $last_indicator = $current_indicator;
                                ?>
                            <?php endif; ?>

                            <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
                                <?php if ($role === 'Admin'): ?>
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th style="width: 9%; text-align: center;">Requirement Code</th>
                                            <th style="width: 25%;">Requirement Description</th>
                                            <th style="width: 9%;text-align: center;">Attachment</th>
                                            <th style="width: 6%;text-align: center;">Status</th>
                                            <th style="width: 10%; text-align: center;"> Last Modified</th>
                                            <th style="width: 7%;text-align: center;"> Approval</th>
                                            <th style="width: 9%;text-align: center;"> Comments</th>
                                        </tr>
                                    </thead>
                                <?php endif; ?>

                                <?php if ($role === 'Secretary'): ?>
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th style="width: 17%;">Relevant/Definition</th>
                                            <th style="width: 17%;">Requirement Description</th>
                                            <th style="width: 6%; text-align: center;">Attachment</th>
                                            <th style="width: 4%; text-align: center;">Status</th>
                                            <th style="width: 9%;"> Last Modified</th>
                                            <th style="width: 7%; text-align: center;"> Comments</th>
                                        </tr>
                                    </thead>
                                <?php endif; ?>

                                <tbody>
                                    <tr>
                                        <td><?php echo $row['relevance_definition']; ?></td>
                                        <td><?php echo $row['reqs_code'] . " " . $row['description']; ?></td>
                                        <?php
                                        $data = $responses->getData($barangay_id, $row['keyctr']);
                                        ?>
                                        <td class="data-cell-upload-view" style="text-align: center; vertical-align: middle;">
                                            <?php if (!$data): ?>
                                                <!-- file is not present -->
                                                <?php if ($role === 'Secretary'): ?>
                                                    <form action="../bar_assessment/user_actions/upload.php" method="POST"
                                                        enctype="multipart/form-data" id="uploadForm-<?php echo $current_row; ?>">
                                                        <input type="hidden" name="barangay_id"
                                                            value="<?php echo htmlspecialchars($barangay_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="criteria_keyctr"
                                                            value="<?php echo htmlspecialchars($row['keyctr'], ENT_QUOTES, 'UTF-8'); ?>">

                                                        <input type="file" name="file" id="file-<?php echo $current_row; ?>"
                                                            class="file-input" style="display: none;" required>

                                                        <button type="button" class="btn btn-primary" title="Upload"
                                                            onclick="document.getElementById('file-<?php echo $current_row; ?>').click();">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </form>


                                                                            <script>
                                                                                document.getElementById('fileInput_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>')
                                                                                    .addEventListener('change', function() {
                                                                                        if (this.files.length > 0) {
                                                                                            document.getElementById('uploadForm_<?php echo htmlspecialchars($current_req_keyctr, ENT_QUOTES, 'UTF-8'); ?>').submit();
                                                                                        }
                                                                                    });
                                                                            </script>


                                                                        <?php elseif ($role === 'Admin'): ?>
                                                                            <!-- Case: Admin Role -->
                                                                            <p>No Uploads Yet</p>
                                                                        <?php endif; ?>

                                                                    <?php else: ?>
                                                                        <!-- Case: File is Present -->
                                                                        <form method="POST" action="../bar_assessment/admin_actions/view.php"
                                                                            target="_blank">
                                                                            <input type="hidden" name="file_id"
                                                                                value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <button type="submit" class="btn btn-success mb-3" title="View">
                                                                                <i class="fa fa-eye"></i>
                                                                            </button>
                                                                        </form>


                                                                        <!-- Additional Options for Secretary -->
                                                                        <?php if ($role === 'Secretary'):
                                                                                                                                    if ($data['status'] != 'approved'):

                                                                        ?>
                                                                                <form id="deleteForm"
                                                                                    data-id="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    <button type="button" class="btn btn-danger mb-3" title="Delete"
                                                                                        onclick="confirmDelete(this)">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                    <script>
                                                        document.querySelectorAll(".file-input").forEach(input => {
                                                            input.addEventListener("change", function () {
                                                                let formId = "uploadForm-" + this.id.split("-")[1];
                                                                let form = document.getElementById(formId);
                                                                if (form) {
                                                                    form.submit();
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                <?php elseif ($role === 'Admin'): ?>
                                                    <p>No Uploads Yet</p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- File is Present -->
                                                <form method="POST" action="../bar_assessment/admin_actions/view.php"
                                                    target="_blank">
                                                    <input type="hidden" name="file_id"
                                                        value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <button type="submit" class="btn btn-success mb-3" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </form>
                                                <?php if ($role === 'Secretary'):
                                                    if ($data['status'] != 'approved'): ?>
                                                   <form id="deleteForm"
                                                                                    data-id="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    <button type="button" class="btn btn-danger mb-3" title="Delete"
                                                                                        onclick="confirmDelete(this)">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </button>
                                                                                </form>

                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Status Icon -->
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
                                            <?php
                                            if ($data):
                                                echo htmlspecialchars($data['date_uploaded']);
                                            endif;

                                            ?>

                                                                </td>

                                                                <?php if ($role === 'Admin'): ?>
                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                        <div class="column">
                                                                            <div class="column">
                                                                                <div class="col-lg-6">
                                                                                    <form method="POST" action="admin_actions/change_status.php">
                                                                                        <input type="hidden" name="file_id"
                                                                                            value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                        <input type="hidden" name="action" value="approve">
                                                                                        <button type="submit" class="btn btn-success mb-3"
                                                                                            style="background-color: #28a745; border: none; color: white; padding: 5px; font-size: 15px; border-radius: 5px; cursor: pointer;">
                                                                                            Approve
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                                <div class="col-lg-6">
                                                                                    <form method="POST" action="admin_actions/change_status.php">
                                                                                        <input type="hidden" name="file_id"
                                                                                            value="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                                        <input type="hidden" name="action" value="decline">
                                                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                                            style="background-color: #dc3545; border: none; color: white; padding: 5px; font-size: 15px; border-radius: 5px; cursor: pointer;">
                                                                                            Decline
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>


                                                                <?php endif; ?>

                                                                <td class="data-cell-comments"
                                                                    style="text-align: center; vertical-align: middle;">
                                                                    <?php
                                                                    if ($data):
                                                                    ?>
                                        </td>
                                        <td class="data-cell-comments" style="text-align: center; vertical-align: middle;">
                                            <?php
                                            if ($data):
                                                ?>

                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#commentModal"
                                                    data-fileid="<?= htmlspecialchars($data['file_id']); ?>"
                                                    data-role="<?= htmlspecialchars($role); ?>"
                                                    data-name="<?= htmlspecialchars($name); ?>">
                                                    Comments
                                                </button>

                                                                    <?php
                                                                    endif;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-white">No minimum requirements found for this indicator.</p>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-white">No indicators found for this description.</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                                                <?php
                                            endif;
                                            ?>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php endforeach; ?>


                        <!-- End of Page Content -->

                        </div>
                </div>
                <!-- End of Main Content -->
            </div>
        </div>
    </div>
    <?php require '../components/comment_section.php'; ?>
</body>

</html>