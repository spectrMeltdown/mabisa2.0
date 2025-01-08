<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
session_set_cookie_params(0);
session_start();

// if (!$_SESSION['id']) {
//   header('location:../actions/logout.php');
// }
require_once '../db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $isLocationPhp = true;
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
            <h1 class="h3 mb-0 text-gray-800">Create Criteria</h1>
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <div class="container mt-5"></div>
            </div>
          </div>
          <script>
          function selectGovernance(option) {
            document.getElementById("dropdownMenuButton").textContent =
              option;
          }
          </script>

          <!-- Content Row -->
          <div class="row">
            <form>
              <div class="form-group"></div>
              <!-- Save Criteria -->
              <!-- Button to open the modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                Open Modal
              </button>

              <!-- The Modal -->
              <div class="modal fade" id="myModal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                      <h4 class="modal-title">Modal Title</h4>
                      <button type="button" class="close" data-dismiss="modal">
                        &times;
                      </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                      <!-- Your modal content here -->
                      <p>This is the modal body.</p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                      </button>
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#saveModal">
                        Save
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- The Save Modal -->
              <div class="modal fade" id="saveModal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                      <h4 class="modal-title">Save Successful</h4>
                      <button type="button" class="close" data-dismiss="modal">
                        &times;
                      </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                      <p>Saved successfully!</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End of Main Content -->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>