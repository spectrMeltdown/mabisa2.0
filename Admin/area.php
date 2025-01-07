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
  <?php require 'common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php 
    $isCriteriaPhp = true;
    require 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <div class="header-title">
            MABILISANG AKSYON INFORMATION SYSTEM OF ALORAN
          </div>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg" />
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../index.php" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Assign Area</h1>
          </div>
          <table id="myTable" class="table table-striped">
            <thead>
              <tr>
                <th>Description</th>
                <th>Trail</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="table-body">
              <!-- rows will be added here dynamically -->
            </tbody>
          </table>
          <button id="add-row-btn" class="btn btn-primary">Add</button>
          <button id="save-btn" class="btn btn-success">Save</button>
          <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
          <script>
          $(document).ready(function() {
            // Add row button click event
            $("#add-row-btn").on("click", function() {
              var newRow = "<tr>";
              newRow +=
                '<td><input type="number" min="0" class="form-control" /></td>';
              newRow +=
                '<td><input type="text" class="form-control" /></td>';
              newRow +=
                '<td><button class="btn btn-danger delete-btn">Delete</button></td>';
              newRow += "</tr>";
              $("#table-body").append(newRow);
            });

            // Save button click event
            $("#save-btn").on("click", function() {
              // TO DO: implement save functionality
              alert("Save button clicked!");
            });

            // Delete button click event
            $(document).on("click", ".delete-btn", function() {
              $(this).closest("tr").remove();
            });
          });
          </script>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin-2.min.js"></script>
</body>

</html>