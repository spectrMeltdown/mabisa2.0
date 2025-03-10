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
    $stmt = $pdo->prepare("SELECT brgyname FROM refbarangay rb WHERE brgyid = ?");
    $stmt->bindParam(1, $barangay_id, PDO::PARAM_INT);
    $stmt->execute();
    $barangay = $stmt->fetch(PDO::FETCH_ASSOC);
    $barangay_name = $barangay ? $barangay['brgyname'] : 'Unknown';



    if ($barangay) {
        $barangay_name = $barangay['brgyname'];
    } else {
        $barangay_name = 'Unknown';
    }

    $stmt = $pdo->prepare("SELECT *  FROM barangay_assessment WHERE barangay_id =?");
    $stmt->execute([$barangay_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ready = !empty($result) ? $result[0]['is_ready'] : 0;
    // Fetch active version
    $stmt = $pdo->prepare("SELECT * FROM maintenance_criteria_version WHERE active_ = 1 LIMIT 1");
    $stmt->execute();
    $version = $stmt->fetch(PDO::FETCH_ASSOC);
    $active_version_keyctr = $version ? $version['keyctr'] : null;

    // Fetch all areas and categories
    $stmt = $pdo->prepare("
        SELECT 
            mg.keyctr AS governance_keyctr,
            mc.description AS category,
            ma.description AS area_description,
            mad.description AS description,
            mad.keyctr AS desc_keyctr
        FROM maintenance_governance AS mg
        LEFT JOIN maintenance_category AS mc ON mg.cat_code = mc.code
        LEFT JOIN maintenance_area AS ma ON mg.area_keyctr = ma.keyctr
        LEFT JOIN maintenance_area_description AS mad ON mg.desc_keyctr = mad.keyctr
        ORDER BY mc.description, ma.description, mad.description
    ");
    $stmt->execute();
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];

    foreach ($areas as $area) {
        // Fetch indicators for each governance area
        $stmt = $pdo->prepare("SELECT * FROM maintenance_area_indicators WHERE governance_code = ?");
        $stmt->execute([$area['governance_keyctr']]);
        $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($indicators as $indicator) {
            // Fetch criteria setup for each indicator
            $stmt = $pdo->prepare("
                SELECT 
                    msc.keyctr AS keyctr,
                    mam.description,
                    mam.reqs_code,
                    mam.relevance_definition,
                    msc.movdocs_reqs AS documentary_requirements,
                    msc.template,
                    mds.srcdesc AS data_source
                FROM maintenance_criteria_setup msc
                LEFT JOIN maintenance_criteria_version AS mcv ON msc.version_keyctr = mcv.keyctr
                LEFT JOIN maintenance_area_mininumreqs AS mam ON msc.minreqs_keyctr = mam.keyctr
                LEFT JOIN maintenance_document_source AS mds ON msc.data_source = mds.keyctr
                WHERE msc.indicator_keyctr = ? AND msc.version_keyctr = ?
                ORDER BY mam.reqs_code ASC
            ");
            $stmt->execute([$indicator['keyctr'], $active_version_keyctr]);
            $criteria = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($criteria as $criterion) {
                $data[$area['category'] . " " . $area['area_description'] . ": " . $area['description']][] = [
                    'keyctr' => $criterion['keyctr'],
                    'indicator_keyctr' => $indicator['keyctr'],
                    'indicator_code' => $indicator['indicator_code'],
                    'indicator_description' => $indicator['indicator_description'],
                    'relevance_definition' => $criterion['relevance_definition'],
                    'reqs_code' => $criterion['reqs_code'],
                    'documentary_requirements' => $criterion['documentary_requirements'],
                    'description' => $criterion['description'],
                    'data_source' => $criterion['data_source'],
                    'template' => $criterion['template'],
                ];
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

                            <?php
                            if (userHasPerms('submissions_create', 'any', $barangay_id) && !str_contains(strtolower($userData['role']), 'super admin')):
                                if ($ready == 1) :
                            ?>
                                    <p class="text-success float-right"><strong>Submitted for Validation</strong></p>
                                <?php else : ?>
                                    <button class="btn btn-success float-right submit-btn" data-bar-id="<?php echo htmlspecialchars($barangay_id); ?>">
                                        Submit for Validation
                                    </button>
                            <?php
                                endif;
                            endif;
                            ?>
                            <?php
                            if (userHasPerms('approve', 'any', $barangay_id) && !str_contains(strtolower($userData['role']), 'super admin')):
                                if ($ready == 0) :
                            ?>
                                    <p class="text-secondary float-right"><strong>Not yet ready for validation</strong></p>
                                <?php else : ?>
                                    <button class="btn btn-success float-right submit-btn" data-reverse="true" data-bar-id="<?php echo htmlspecialchars($barangay_id); ?>">
                                        Validate
                                    </button>
                            <?php
                                endif;
                            endif;
                            ?>
                        </div>


                    </div>
                    <!-- hide all submissions until ready for validation -->

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
                                                                    if (!str_contains(strtolower($userData['role']), 'admin') && userHasPerms('submissions_create', 'any', $barangay_id, $row['indicator_keyctr']) && $version['is_accepting_response'] == '0' && $ready == 0) : ?>
                                                                        <form action="../bar_assessment/user_actions/upload.php" method="POST"
                                                                            enctype="multipart/form-data" id="uploadForm-<?php echo $row['keyctr']; ?>">
                                                                            <input type="hidden" name="iid" value="<?= $row['indicator_keyctr'] ?>">
                                                                            <input type="hidden" name="expand" value="collapse-<?php echo md5($key); ?>">
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


                                                                    <?php if (
                                                                        (userHasPerms('approve', 'any', $barangay_id, $row['indicator_keyctr'])
                                                                            && $ready == 1) ||
                                                                        (userHasPerms('create', 'any', $barangay_id, $row['indicator_keyctr']))
                                                                    ): ?>
                                                                        <button type="button" class="btn btn-success mb-3" title="View"
                                                                            data-toggle="modal" data-target="#commentModal"
                                                                            data-fileid="<?= htmlspecialchars($data['file_id']); ?>"
                                                                            data-name="<?= htmlspecialchars($name); ?>"
                                                                            data-status="<?= htmlspecialchars($data['status']); ?>"
                                                                            data-bid="<?= htmlspecialchars($barangay_id); ?>"
                                                                            data-ready="<?= htmlspecialchars($ready); ?>"
                                                                            data-iid="<?= htmlspecialchars($row['indicator_keyctr']); ?>"
                                                                            data-expand="collapse-<?php echo md5($key); ?>">
                                                                            <i class="fa fa-eye"></i>
                                                                        </button>
                                                                    <?php endif; ?>

                                                                    <?php if (
                                                                        !str_contains(strtolower($userData['role']), 'admin')
                                                                        && userHasPerms('submissions_delete', 'any', $barangay_id, $row['indicator_keyctr'])
                                                                        && $data['status'] !== 'approved'
                                                                        && $ready == 0
                                                                    ): ?>
                                                                        <button class="btn btn-danger mb-3 delete-btn"
                                                                            data-file-id="<?php echo htmlspecialchars($data['file_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                            data-bid="<?= htmlspecialchars($barangay_id); ?>"
                                                                            data-iid="<?= htmlspecialchars($row['indicator_keyctr']); ?>"
                                                                            data-expand="collapse-<?php echo md5($key); ?>">
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
                                                                            <p>Awaiting validation</p>
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
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i> Scroll to Top
        </a>
    </div>
    </div>
    <?php require_once '../components/comment_section.php'; ?>
    <?php require_once '../components/all_comments.php'; ?>
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
                        .then(async response => {
                            const text = await response.text();
                            console.log(text);
                            return JSON.parse(text);
                        })
                        .then(data => {
                            if (data.success) {
                                alert("File uploaded successfully!");
                                if (formData.has('barangay_id') && formData.has('iid') && formData.has('expand')) {
                                    console.log('has all three');
                                    console.log(formData.get('barangay_id'));
                                    console.log(formData.get('iid'));
                                    console.log(formData.get('expand'));
                                    let url = new URL(location.href.split("&")[0]);
                                    url.searchParams.set('expand', ('#' + formData.get('expand')));
                                    location.href = (url.toString() + '#' + formData.get('barangay_id') + formData.get('iid'));
                                    setTimeout(() => {
                                        location.reload();
                                    }, 500);
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
                let fileId = e.target.closest('.delete-btn').getAttribute('data-file-id');

                if (!confirm('Are you sure you want to delete this file?')) {
                    return;
                }
                $.ajax({
                    url: '../bar_assessment/user_actions/delete.php',
                    method: 'POST',
                    data: {
                        file_id: fileId
                    },
                    success: data => {
                        console.log(data);
                        if (data.success) {
                            alert('File deleted successfully.');
                            const bid = e.target.dataset.bid;
                            const iid = e.target.dataset.iid;
                            const expand = e.target.dataset.expand;
                            if (bid && iid && expand) {
                                console.log('has all three');
                                console.log(bid);
                                console.log(iid);
                                console.log(expand);
                                let url = new URL(location.href.split("&expand")[0]);
                                url.searchParams.set('expand', ('#' + expand));
                                location.href = (url.toString() + '#' + bid + iid);
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            } else {
                                console.log('nope');
                                location.reload();
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: err => {
                        console.error(err.responseText());
                    },
                });
            });
        });

        document.querySelectorAll('.submit-btn').forEach(button => {
            button.addEventListener('click', e => {
                let barangayid = e.target.closest('.submit-btn').getAttribute('data-bar-id');
                let isReverse = e.target.closest('.submit-btn').getAttribute('data-reverse') ?? '';
                console.log('in submit btn');
                console.log(barangayid);
                console.log(isReverse);

                if (isReverse == 'true') {
                    if (!confirm('Are you sure you are done validating?')) {
                        return;
                    }
                } else {
                    if (!confirm('Are you sure you want to submit for validation?')) {
                        return;
                    }
                }
                fetch('../bar_assessment/user_actions/validate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            barangay_id: barangayid,
                            isReverse: isReverse
                        })
                    })
                    .then(async response => {
                        const text = await response.text();
                        console.log(text);
                        return JSON.parse(text);
                    })
                    .then(data => {
                        if (data.success) {
                            if (data.isReverse) {
                                alert('Validation status updated successfully.');
                            } else {
                                alert('Submitted for validation successfully.');
                            }
                            const bid = e.target.dataset.bid;
                            const iid = e.target.dataset.iid;
                            const expand = e.target.dataset.expand;
                            if (bid && iid && expand) {
                                console.log('has all three');
                                console.log(bid);
                                console.log(iid);
                                console.log(expand);
                                let url = new URL(location.href.split("&")[0]);
                                url.searchParams.set('expand', ('#' + expand));
                                location.href = (url.toString() + '#' + bid + iid);
                                // setTimeout(() => {
                                //     location.reload();
                                // }, 500);
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
</body>

</html>