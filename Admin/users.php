<!DOCTYPE html>
<html lang="en">

<head>
  <?php require('common/head.php') ?>
  <title>MABISA - Users</title>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <?php require('common/sidebar.php') ?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <?php require('common/nav.php') ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Users</h1>
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
                Add user
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
            </form>
          </div>

          <!-- user table -->
          <table class="table table-bordered mt-3">
            <thead>
              <tr>
                <!--  Intentionally left blank for checkboxes column -->
                <th><input type="checkbox"></th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Barangay</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php

              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
<!-- Bootstrap core JavaScript-->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="../js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="../vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="../js/demo/chart-area-demo.js"></script>
<script src="../js/demo/chart-pie-demo.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../js/users.js"></script>

</html>