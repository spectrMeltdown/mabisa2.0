<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location: Admin/logged_out.php');
  exit;
}

require_once 'db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $root = true;
  require 'Admin/common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php require 'Admin/common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <?php require 'Admin/common/nav.php' ?>
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
                          0
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
                          0
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
                          0
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
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Area Code</th>
                <th scope="col">Area Name</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">1</th>
                <td>Financial Administration and Sustainability</td>
              </tr>
              <tr>
                <th scope="row">2</th>
                <td>Disaster Preparedness</td>
              </tr>
              <tr>
                <th scope="row">3</th>
                <td colspan="2">Safety Peace and Order</td>
              </tr>
            </tbody>
          </table>
          <!-- Content Column -->
          <div class="col-lg-6 mb-4"></div>
        </div>

        <!-- History Logs -->
        <div class="row">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Logs</h5>
            </div>
            <div class="card-body">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">User ID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Action</th>
                    <th scope="col">Date/Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>john.doe</td>
                    <td>Logged in</td>
                    <td>2024-03-01 14:30:00</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>jane.doe</td>
                    <td>Viewed dashboard</td>
                    <td>2024-03-01 14:35:00</td>
                  </tr>
                  <tr>
                    <td>1</td>
                    <td>john.doe</td>
                    <td>Updated profile</td>
                    <td>2024-03-01 14:40:00</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>admin</td>
                    <td>Deleted user</td>
                    <td>2024-03-01 14:45:00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright &copy; MABISA 2024</span>
            </div>
          </div>
        </footer>
        <!-- End of Footer -->
      </div>
      <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              Ready to Leave?
            </h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            Select "Logout" below if you are ready to end your current
            session.
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">
              Cancel
            </button>
            <a class="btn btn-primary" href="index.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>