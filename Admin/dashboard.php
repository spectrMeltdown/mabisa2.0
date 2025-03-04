<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require_once 'common/auth.php';
require_once '../db/db.php';
require_once 'bar_assessment/responses.php';


$responses = new Responses($pdo);

$barangayData = $pdo->query('SELECT brgyid, brgyname FROM refbarangay')->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];

foreach ($barangayData as $barangay) {
    $responseCount = $responses->getResponseCount($barangay['brgyid']);
    
    // Extract the first number from "X/Y"
    $submitted = explode('/', $responseCount)[0];

    $labels[] = $barangay['brgyname'];
    $data[] = (int)$submitted;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require_once 'common/head.php' ?>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <!-- <script src="../js/demo/chart-bar-demo.js" defer></script> -->
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
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

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
              <h4 class="m-0 font-weight-bold text-primary">
                Barangay Progress
              </h4>
            </div>
            <div class="card-body">
            <div style="overflow-x: auto; width: 100%;">
  <div style="width: 1500px;">
    <canvas id="myBarChart"></canvas>
  </div>
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
          $sql = "SELECT * FROM audit_log WHERE DATE(time_and_date) = CURDATE() ORDER BY time_and_date DESC";
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
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h3 class="m-0 font-weight-bold text-primary">Activity Logs Today</h3>
              <a href="user_log.php" class="btn btn-danger">Show All Logs</a>
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
  <script>
$(document).ready(function () {
  $.ajax({
    url: "chart_data.php", 
    method: "GET",
    dataType: "json",
    success: function (response) {
      if (!response || response.labels.length === 0) {
        console.error("No data returned for chart.");
        return;
      }

      var ctx = document.getElementById("myBarChart").getContext("2d");

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: response.labels,
          datasets: [
            {
              label: "Submissions",
              backgroundColor: "#4e73df",
              hoverBackgroundColor: "#2e59d9",
              borderColor: "#4e73df",
              data: response.data,
            },
          ],
        },
        options: {
          maintainAspectRatio: false,
          layout: {
            padding: { left: 10, right: 25, top: 25, bottom: 10 },
          },
          scales: {
            xAxes: [{
              categoryPercentage: 0.6, // Increase spacing between bars
              barPercentage: 0.5, // Adjust bar width
              gridLines: { display: false },
              ticks: { 
                autoSkip: false,
                padding: 15, // space between label and bar
                fontSize: 14, // label size
                 maxRotation: 45, //label angle
                minRotation: 45 
              }
            }],
            yAxes: [{
              ticks: { 
                beginAtZero: true, 
                stepSize: 5, //increments per label
                fontSize: 14 //y axes label size
              },
              gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2],
              },
            }],
          },
          legend: { display: false },
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Error loading chart data:", error);
    },
  });
});

// Make the chart container taller
document.getElementById("myBarChart").parentNode.style.height = "300px";

  </script>
</body>

</html>