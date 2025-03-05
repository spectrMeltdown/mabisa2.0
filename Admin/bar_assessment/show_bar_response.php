<?php
$isInFolder = true;
require_once '../common/auth.php';
$barangay_id = isset($_GET['barangay_id']) ? $_GET['barangay_id'] : null;
$barangay_name = isset($_GET['brgyname']) ? $_GET['brgyname'] : null;
if (!userHasPerms('assessment', 'any')) {
    // header does not allow relative paths, so this is my temporary solution
    header('Location:' . substr(__DIR__, 0, strrpos(__DIR__, '/')) . 'no_permissions.php');
    exit;
}

require_once 'responses.php';
require_once 'comments.php';
require_once 'admin_actions/admin_actions.php';
require_once '../../db/db.php';
require_once '../../api/audit_log.php';

$responses = new Responses($pdo);
$logging = new Audit_log($pdo);

if ($barangay_id) {
    $stmt = $pdo->prepare("SELECT brgyname FROM refbarangay WHERE brgyid = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $barangay = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("SELECT is_ready FROM barangay_assessment WHERE barangay_id = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $ready = $result['is_ready'] ?? 0;
    if ($barangay) {
        $barangay_name = $barangay['brgyname'];
    } else {
        $barangay_name = 'Unknown';
    }

    $stmt = $pdo->prepare("SELECT * FROM barangay_assessment_files WHERE barangay_id = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include '../script.php';
    $data = [];

    $stmt = $pdo->prepare("SELECT * FROM maintenance_criteria_version WHERE active_ = 1 LIMIT 1");
    $stmt->execute();
    $version = $stmt->fetch(PDO::FETCH_ASSOC);
    $active_version_keyctr = $version ? $version['keyctr'] : null;

    $maintenance_area_description_query = "
    SELECT
        mg.*,
        mc.description AS category,
        ma.description AS area_description,
        mad.description AS description
    FROM maintenance_governance AS mg
    LEFT JOIN maintenance_category AS mc 
        ON mg.cat_code = mc.code
    LEFT JOIN maintenance_area AS ma 
        ON mg.area_keyctr = ma.keyctr
    LEFT JOIN maintenance_area_description AS mad 
        ON mg.desc_keyctr = mad.keyctr;
";


    $stmt = $pdo->prepare($maintenance_area_description_query);
    $stmt->execute();
    $maintenance_area_description_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($maintenance_area_description_result)) {
        foreach ($maintenance_area_description_result as $maintenance_area_description_row) {

            $maintenance_governance_query = "SELECT * FROM `maintenance_governance` WHERE desc_keyctr = :desc_keyctr";
            $stmt = $pdo->prepare($maintenance_governance_query);
            $stmt->execute(['desc_keyctr' => $maintenance_area_description_row['keyctr']]);
            $maintenance_governance_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($maintenance_governance_result)) {
                foreach ($maintenance_governance_result as $maintenance_governance_row) {

                    $maintenance_area_indicators_query = "SELECT * FROM `maintenance_area_indicators` WHERE governance_code = :governance_code";
                    $stmt = $pdo->prepare($maintenance_area_indicators_query);
                    $stmt->execute(['governance_code' => $maintenance_governance_row['keyctr']]);
                    $maintenance_area_indicators_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($maintenance_area_indicators_result)) {
                        foreach ($maintenance_area_indicators_result as $maintenance_area_indicators_row) {

                            $maintenance_criteria_setup_query = "
                                SELECT 
                                    msc.keyctr AS keyctr,
                                    mam.description,
                                    mam.reqs_code,
                                    msc.movdocs_reqs AS documentary_requirements,
                                    msc.template,
                                    mds.srcdesc AS data_source
                                FROM `maintenance_criteria_setup` msc 
                                LEFT JOIN maintenance_criteria_version AS mcv ON msc.version_keyctr = mcv.keyctr
                                LEFT JOIN maintenance_area_mininumreqs AS mam ON msc.minreqs_keyctr = mam.keyctr
                                LEFT JOIN maintenance_document_source AS mds ON msc.data_source = mds.keyctr 
                                WHERE msc.indicator_keyctr = :indicator_keyctr
                                AND msc.version_keyctr = :active_version_keyctr
                                ORDER BY mam.reqs_code ASC
                            ";

                            $stmt = $pdo->prepare($maintenance_criteria_setup_query);
                            $stmt->execute([
                                'indicator_keyctr' => $maintenance_area_indicators_row['keyctr'],
                                'active_version_keyctr' => $active_version_keyctr
                            ]);
                            $maintenance_criteria_setup_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($maintenance_criteria_setup_result)) {
                                foreach ($maintenance_criteria_setup_result as $maintenance_criteria_setup_row) {
                                    $data[$maintenance_area_description_row['category'] . " " .
                                        $maintenance_area_description_row['area_description'] . ": " .
                                        $maintenance_area_description_row['description']][] = [
                                        'keyctr' => $maintenance_criteria_setup_row['keyctr'],
                                        'indicator_keyctr' => $maintenance_area_indicators_row['keyctr'],
                                        'indicator_code' => $maintenance_area_indicators_row['indicator_code'],
                                        'indicator_description' => $maintenance_area_indicators_row['indicator_description'],
                                        'relevance_definition' => $maintenance_area_indicators_row['relevance_def'],
                                        'reqs_code' => $maintenance_criteria_setup_row['reqs_code'],
                                        'documentary_requirements' => $maintenance_criteria_setup_row['documentary_requirements'],
                                        'description' => $maintenance_criteria_setup_row['description'],
                                        'data_source' => $maintenance_criteria_setup_row['data_source'],
                                        'template' => $maintenance_criteria_setup_row['template'],
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
// print_r($ready);
// echo'</pre>';
session_start();
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $pathPrepend = '../../';
    require_once '../common/head.php';
    ?>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../js/bar-assessment.js" defer></script>

    <style>
        table {
            border-collapse: collapse !important;
            border: 1px solid black !important;
        }

        th,
        td {
            border: 1px solid black !important;
            padding: 10px !important;
            text-align: center !important;
        }
    </style>
</head>


<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--sidebar start  -->
        <?php
        include_once '../common/sidebar.php';
        ?>
        <!-- sidebar end -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php
                include_once '../common/nav.php';
                $name = $userData['full_name'];
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
                            <button class="btn btn-info" onclick="fetchAllComments(<?php echo $barangay_id; ?>)" data-toggle="modal" data-target="#allCommentsModal">
                                View All Comments Summary
                            </button>

                            <?php if ($ready == 1) : ?>
                                <p class="text-success float-right"><strong>Submitted for Validation</strong></p>
                            <?php else : ?>
                                <button class="btn btn-success float-right submit-btn" data-bar-id="<?php echo htmlspecialchars($barangay_id); ?>">
                                    Submit for Validation
                                </button>
                            <?php endif; ?>
                        </div>


                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <?php if ($data) { ?>
                                <?php foreach ($data as $key => $rows): ?>
                                    <!-- Collapsible Button -->
                                    <div class="card-header bg-primary text-center py-3"
                                        id="collapseBtn-<?php echo md5($key); ?>"
                                        data-toggle="collapse"
                                        data-target="#collapse-<?php echo md5($key); ?>"
                                        aria-expanded="false"
                                        style="cursor: pointer;">
                                        <h5 class="text-white mb-0">
                                            <?php echo htmlspecialchars($key); ?>
                                        </h5>
                                    </div>

                                    <!-- Collapsible Content -->
                                    <div id="collapse-<?php echo md5($key); ?>" class="collapse">
                                        <div class="card-body">
                                            <?php
                                            $last_indicator = '';
                                            $table_started = false;
                                            $requirement_counts = [];
                                            foreach ($rows as $row) {
                                                $req_key = $row['reqs_code'] . " " . $row['description'];
                                                if (!isset($requirement_counts[$req_key])) {
                                                    $requirement_counts[$req_key] = 0;
                                                }
                                                $requirement_counts[$req_key]++;
                                            }

                                            $printed_reqs = [];

                                            foreach ($rows as $row):
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
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 17%; text-align: center;">Requirement Description</th>
                                                                <th style="width: 17%;">Requirement MOV's</th>
                                                                <th style="width: 9%; text-align: center;">Attachment</th>
                                                                <th style="width: 6%; text-align: center;">Status</th>
                                                                <th style="width: 9%; text-align: center;">Last Modified</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $last_indicator = $current_indicator;
                                                        $table_started = true;
                                                    endif;

                                                    $req_key = $row['reqs_code'] . " " . $row['description'];
                                                        ?>
                                                        <tr>
                                                            <?php if (!isset($printed_reqs[$req_key])): ?>
                                                                <td rowspan="<?= $requirement_counts[$req_key]; ?>">
                                                                    <span class="short-text">
                                                                        <?= htmlspecialchars(substr($req_key, 0, 300)) . '...'; ?>
                                                                    </span>
                                                                    <span class="full-text" style="display: none;">
                                                                        <?= htmlspecialchars($req_key); ?>
                                                                    </span>
                                                                    <a href="#" class="see-more">See more</a>
                                                                </td>
                                                                <?php $printed_reqs[$req_key] = true; ?>
                                                            <?php endif; ?>

                                                            <td>
                                                                <?php
                                                                $link = htmlspecialchars($row['template']);
                                                                echo htmlspecialchars($row['documentary_requirements']) . '<br> <br>';
                                                                if (!empty($link)) {
                                                                    echo '<a href="' . $link . '" target="_blank">View Template</a>';
                                                                } else {
                                                                    echo 'No template available';
                                                                }
                                                                ?>
                                                            </td>
                                                            <?php
                                                            $data = $responses->getData($barangay_id, $row['keyctr']);
                                                            ?>
                                                            <td class="data-cell-upload-view" style="text-align: center; vertical-align: middle;" id="<?php echo htmlspecialchars($barangay_id . $row['indicator_keyctr']) ?>">
                                                                <?php if (!$data): ?>
                                                                    <?php
                                                                    if (!str_contains(strtolower($userData['role']), 'admin') && userHasPerms('submissions_create', 'any', $barangay_id, $row['indicator_keyctr']) && $version['is_accepting_response'] == '0') : ?>
                                                                        <form action="../bar_assessment/user_actions/upload.php" method="POST"
                                                                            enctype="multipart/form-data" id="uploadForm-<?php echo $row['keyctr']; ?>">
                                                                            <input type="hidden" name="iid" value="<?= $row['indicator_keyctr'] ?>">
                                                                            <input type="hidden" name="expand" value="#collapse-<?php echo md5($key); ?>">
                                                                            <input type="hidden" name="barangay_id"
                                                                                value="<?php echo htmlspecialchars($barangay_id, ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="criteria_keyctr"
                                                                                value="<?php echo htmlspecialchars($row['keyctr'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="file" name="file" id="file-<?php echo $row['keyctr']; ?>"
                                                                                class="file-input" style="display: none;" required accept="application/pdf">
                                                                            <button type="button" class="btn btn-primary" title="Upload"
                                                                                onclick="document.getElementById('file-<?php echo $row['keyctr']; ?>').click();">
                                                                                <i class="fa fa-upload"></i>
                                                                            </button>
                                                                        </form>
                                                                        <?php else:
                                                                        if ($version['is_accepting_response'] == '1') : ?>
                                                                            <p>Submission Closed</p>
                                                                        <?php else: ?>
                                                                            <p>No Uploads Yet</p>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-success mb-3" title="View"
                                                                        data-toggle="modal" data-target="#commentModal"
                                                                        data-fileid="<?= htmlspecialchars($data['file_id']); ?>"
                                                                        data-name="<?= htmlspecialchars($name); ?>"
                                                                        data-status="<?= htmlspecialchars($data['status']); ?>"
                                                                        data-bid="<?= htmlspecialchars($barangay_id); ?>"
                                                                        data-iid="<?= htmlspecialchars($row['indicator_keyctr']); ?>"
                                                                        data-expand="#collapse-<?php echo md5($key); ?>">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button>
                                                                    <?php if (!str_contains(strtolower($userData['role']), 'admin') && userHasPerms('submissions_delete', 'any', $barangay_id, $row['indicator_keyctr']) && $data['status'] !== 'approved'): ?>
                                                                        <button class="btn btn-danger mb-3 delete-btn"
                                                                            data-file-id="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                            data-bid="<?= htmlspecialchars($barangay_id); ?>"
                                                                            data-iid="<?= htmlspecialchars($row['indicator_keyctr']); ?>"
                                                                            data-expand="#collapse-<?php echo md5($key); ?>">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="data-cell-status" style="text-align: center; vertical-align: middle;">
                                                                <?php if (!empty($data)): ?>
                                                                    <?php if ($data['status'] === 'approved'): ?>
                                                                        <div class="rounded bg-success text-white">
                                                                            <p>Approved</p>
                                                                        </div>
                                                                    <?php elseif ($data['status'] === 'declined'): ?>
                                                                        <div class="rounded bg-danger text-white">
                                                                            <p>Returned</p>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="rounded bg-secondary text-white">
                                                                            <p>Waiting for Approval</p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <p>--</p>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="data-cell-date-uploaded" style="text-align: center; vertical-align: middle;">
                                                                <?php echo !empty($data) ? htmlspecialchars($data['date_uploaded']) : '--'; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>

                                                    <?php if ($table_started) {
                                                        echo "</tbody></table>";
                                                    } ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php } else { ?>
                                <div style="display: flex; justify-content: center;">
                                    No Requirements Yet
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
            <!-- End of Main Content -->
        </div>
    </div>
    </div>
    <script>
        var successMessage = "<?php echo $successMessage; ?>";
        if (successMessage) {
            alert(successMessage);
        }

        document.querySelectorAll(".file-input").forEach(input => {
            input.addEventListener("change", function() {
                let formId = "uploadForm-" + this.id.split("-")[1];
                let form = document.getElementById(formId);

                if (this.files.length > 0) {
                    let file = this.files[0];
                    let fileName = file.name;
                    let fileSize = file.size;
                    let maxFileSize = 10 * 1024 * 1024; // change first number to change file size limit

                    if (fileSize > maxFileSize) {
                        alert("File size exceeds 10MB limit.");
                        this.value = "";
                        return;
                    }

                    let formData = new FormData(form);
                    formData.append("file", file);

                    fetch(form.action, {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("File uploaded successfully!");
                                if (formData.has('barangay_id') && formData.has('iid') && formData.has('expand')) {
                                    console.log('has all three');
                                    let url = new URL(location.href);
                                    url.searchParams.set('expand', formData.get('expand'));
                                    location.href = (url.toString() + '#' + formData.get('barangay_id') + formData.get('iid'));
                                } else {
                                    console.log('nope');
                                    location.reload();
                                }
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                }
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', e => {
                let fileId = e.target.getAttribute('data-file-id');

                if (!confirm('Are you sure you want to delete this file?')) {
                    return;
                }

                fetch('../bar_assessment/user_actions/delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            file_id: fileId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('File deleted successfully.');
                            const bid = e.target.dataset.bid;
                            const iid = e.target.dataset.iid;
                            const expand = e.target.dataset.expand;
                            if (bid && iid && expand) {
                                console.log('has all three');
                                let url = new URL(location.href);
                                url.searchParams.set('expand', expand);
                                location.href = (url.toString() + '#' + bid + iid);
                            } else {
                                console.log('nope');
                                location.reload();
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.submit-btn').forEach(button => {
            button.addEventListener('click', e => {
                let barangayid = e.target.getAttribute('data-bar-id');

                if (!confirm('Are you sure you want to submit for validation? This process cannot be undone')) {
                    return;
                }

                console.log(barangayid);

                fetch('../bar_assessment/user_actions/validate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            barangay_id: barangayid
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Submitted for validation successfully.');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".see-more").forEach(function(link) {
                link.addEventListener("click", function(event) {
                    event.preventDefault();
                    let parent = this.parentElement;
                    parent.querySelector(".short-text").style.display = "none";
                    parent.querySelector(".full-text").style.display = "inline";
                    this.style.display = "none";
                });
            });
        });

        function fetchAllComments(barangayId) {
            $.ajax({
                url: '../bar_assessment/fetch_all_comments.php',
                type: 'POST',
                data: {
                    barangay_id: barangayId
                },
                success: function(response) {

                    $('#allCommentsContainer').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch all comments:', error);
                }
            });
        }

        $(document).ready(function() {
    $(document).on("click", ".go-to-file", function() {
        let fileId = $(this).data("fileid");

        $("#allCommentsModal").modal("hide");

        setTimeout(function() {
            $('button[data-target="#commentModal"][data-fileid="' + fileId + '"]').trigger("click");
        }, 500);
    });
});

    </script>


    <?php require_once '../components/comment_section.php'; ?>
    <?php require_once '../components/all_comments.php'; ?>
</body>

</html>