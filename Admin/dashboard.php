<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require 'common/auth.php';
require_once '../db/db.php';
require_once 'bar_assessment/responses.php';

$responses = new Responses($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require 'common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php require 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <?php require 'common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate
              Report</a>
          </div>

          <div class="container">
            <!-- Content Row -->
            <div class="row justify-content-center">
              <!-- In progress Barangays -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                          Barangay In Progress
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php
                          $stmt = $pdo->query("SELECT barangay_id FROM `barangay_assessment`;");
                          $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

                          $complete = 0;

                          foreach ($barangays as $barangay) {
                            $progress = $responses->getProgress($barangay['barangay_id']);

                            if ($progress != 100) {
                              $complete++;
                            }
                          }

                          echo $complete;
                          ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Completed Barangay -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                          Completed Barangay
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php
                          $stmt = $pdo->query("SELECT barangay_id FROM `barangay_assessment`;");
                          $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

                          $complete = 0;

                          foreach ($barangays as $barangay) {
                            $progress = $responses->getProgress($barangay['barangay_id']);

                            if ($progress == 100) {
                              $complete++;
                            }
                          }

                          echo $complete;
                          ?>


                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Registered Barangay -->
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                          Registered Barangay
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                          <?php
                          $stmt = $pdo->query("SELECT COUNT(*) FROM barangay_assessment;");
                          $count = $stmt->fetchColumn();
                          echo $count;
                          ?>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Row -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">
                Barangay Progress
              </h6>
            </div>
            <div class="card-body">
              <div class="chart-area">
                <canvas id="myAreaChart"></canvas>
              </div>
            </div>
          </div>
          <!-- Content Row -->
          <div class="row"></div>
          <!-- Content Column -->
          <div class="col-lg-6 mb-4"></div>
        </div>

        <?php
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
        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">User Logs</h3>
              </div>
            </div>
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
        <?php include 'common/footer.php' ?>
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