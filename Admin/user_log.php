<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require_once 'common/auth.php';
require_once '../db/db.php';
require_once 'bar_assessment/responses.php';

$responses = new Responses($pdo);

require_once '../db/db.php';

try {
    $sql = "SELECT * FROM audit_log ORDER BY time_and_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='text-danger'>Error fetching logs: " . $e->getMessage() . "</p>";
    $logs = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once 'common/head.php' ?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../js/demo/chart-bar-demo.js" defer></script>
    <script src="../js/maintenance-criteria.js"></script>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php require_once 'common/sidebar.php' ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php require_once 'common/nav.php' ?>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Activity Logs</h1>
                        <a href="dashboard.php" class="btn btn-secondary">Back</a>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="card shadow mb-4">

                        <div class="card-body">
                            <div class="table table-responsive"></div>
                            <table id="maintenanceTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>User Id</th>
                                        <th>Username</th>
                                        <th>Action</th>
                                        <th>Time and Date</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log):
                                    ?>
                                        <tr>
                                            <td><?php echo $log['user_id']; ?></td>
                                            <td><?php echo $log['username']; ?></td>
                                            <td><?php echo $log['action']; ?></td>
                                            <td><?php echo $log['time_and_date']; ?></td>

                                        </tr>

                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <!--End Page Content-->
                        </div>
                    </div>
                </div>

                <!-- End of Main Content -->
                <?php include_once 'common/footer.php' ?>
            </div>
            <!-- End of Content Wrapper -->
        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>
</body>

</html>