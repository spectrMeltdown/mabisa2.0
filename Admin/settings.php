<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'common/head.php' ?>
  <script src="../js/settings.js" defer></script>
  <script src="../js/confirmation.js" defer></script>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <?php
    require 'common/sidebar.php' ?>
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
          <!-- <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Settings</h3>
              </div>
            </div>
            <div class="card-body" id="viewLocation">
              <div class="table-responsive">
              </div>
            </div>
          </div>
        </div> -->
          <div class="container mt-5">
            <div class="card">
              <div class="card-header">
                <h3 class="m-0 font-weight-bold text-primary">Settings</h3>
              </div>
              <div class="card-body">
                <!-- Profile Picture Section -->
                <div class="d-flex flex-column justify-content-center align-items-center mb-4 mw-75">
                  <img id="profilePic" src="../img/undraw_profile_2.svg" class="rounded-circle" alt="Profile Picture" width="150" height="150">
                  <button class="btn btn-primary mt-2 mw-25" id="changePicBtn">Change Profile Picture</button>
                  <input type="file" id="profilePicInput" accept="image/*" style="display: none;">
                </div>
                <br>

                <!-- User Details Section -->
                <ul class="list-group mb-4">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Username:</strong> <span id="username">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" data-field="username">Edit</button>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Email:</strong> <span id="email">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" data-field="email">Edit</button>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Full Name:</strong> <span id="fullName">Loading...</span></span>
                    <button class="btn btn-sm btn edit-btn" data-field="fullName">Edit</button>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Mobile Number:</strong> <span id="mobileNum">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" data-field="mobileNum">Edit</button>
                  </li>
                </ul>

                <!-- disabled because the super admin should be the only one to manage accounts -->
                <!-- <button class="btn btn-danger btn-block" id="deleteAccountBtn">Delete Account</button> -->
              </div>
            </div>
          </div>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel">Edit <span id="fieldLabel"></span></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="editForm">
                    <div class="form-group">
                      <label for="newValue">New <span id="fieldLabelInput"></span>:</label>
                      <input type="text" class="form-control" id="newValue" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
      </div>
      <!-- End of Page Wrapper -->
      <?php require 'components/confirmation_dialog.php' ?>
</body>

</html>