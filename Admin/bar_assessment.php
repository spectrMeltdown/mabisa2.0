<?php
require_once 'common/auth.php';
if (!userHasPerms('assessment', 'any')) {
    header('Location:/mabisa/Admin/no_permissions.php');
    exit;
}

require_once '../db/db.php';
require_once 'bar_assessment/responses.php';

$responsesObj = new Responses($pdo);
$responses = $responsesObj->show_responses();

$stmt = $pdo->prepare("SELECT * FROM maintenance_criteria_version WHERE active_ = 1 LIMIT 1");
$stmt->execute();
$version = $stmt->fetch(PDO::FETCH_ASSOC);
$duration = $version ? $version['duration'] : null;
$is_accepting = isset($version['is_accepting_response']) ? ($version['is_accepting_response'] ? 'Yes' : 'No') : null;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'common/head.php'; ?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../js/bar-assessment.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--sidebar start  -->
        <?php
        $isInFolderessmentPhp = true;
        require_once 'common/sidebar.php' ?>
        <!-- sidebar end -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">


            <div id="content">

                <!-- Topbar -->
                <?php include_once 'common/nav.php' ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h3 class="m-0 font-weight-bold text-primary">Barangay Assessment</h3>
                            <div class="text-right">
                                <p class="mb-1"><strong>Duration:</strong> <?php echo $duration; ?></p>
                                <p class="mb-0"><strong>Accepting Responses:</strong> <?php echo $is_accepting ? 'Yes' : 'No'; ?></p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table table-responsive">
                                <table id="barangayTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Barangay</th>
                                            <th>Last Transaction</th>
                                            <th>Actions</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($responses)) {
                                            foreach ($responses as $row) {
                                                $isPassed = $responsesObj->passedOrFail($row['barangay_id']);
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['barangay']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['dateUploaded']) . "</td>";
                                                echo "<td>";
                                                echo "<a href='bar_assessment/show_bar_response.php?barangay_id=" . htmlspecialchars($row['barangay_id']) . "' class='btn btn-primary btn-sm'>View</a>";
                                                echo "</td>";
                                                if ($isPassed >= 40) {
                                                    echo "<td> Passed </td>";
                                                } else {
                                                    echo "<td> Failed </td>";
                                                }
                                                echo "</tr>";
                                                // echo "<td>" . $isPassed . "</td>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->
            </div>
            <!-- End of Main Content -->
        </div>
    </div>
    </div>
</body>

</html>