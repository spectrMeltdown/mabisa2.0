<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
require_once 'common/auth.php';
require_once '../db/db.php';
global $userData;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'common/head.php'; ?>
  <script src="../js/settings.js" defer></script>
  <script src="../js/util/confirmation.js" defer></script>
  <script src="../js/util/phone-prepend.js" defer></script>
  <script src="../js/util/file-link-exists.js" defer></script>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <?php
    require_once 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">


      <div id="content">

        <!-- Topbar -->
        <?php require_once 'common/nav.php' ?>
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
          <div class="container my-5">
            <div class="card">
              <div class="card-header">
                <h3 class="m-0 font-weight-bold text-primary">Settings</h3>
              </div>
              <div class="card-body">
                <!-- Profile Picture Section -->
                <div class="d-flex flex-column justify-content-center align-items-center mb-4 mw-75">
                  <img id="profilePic" src="" class="rounded-circle border border-primary" alt="Profile Picture" width="150" height="150">
                  <button class="btn btn-primary mt-2 mw-25" id="changePicBtn">Change Profile Picture</button>
                  <input type="file" id="fileInput" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp" style="display: none;">
                  <div id="alertDiv" style="color: red; display: none;"></div>
                </div>
                <br>

                <!-- User Details Section -->
                <ul class="list-group mb-4">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Username:</strong> <span id="username">Loading...</span></span>
                    <?php if (userHasPerms('user_create', 'gen')):  ?>
                      <button class="btn btn-sm btn-secondary edit-btn" id="username-edit">Edit</button>
                    <?php endif; ?>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Email:</strong> <span id="email">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" id="email-edit">Edit</button>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Full Name:</strong> <span id="fullName">Loading...</span></span>
                    <?php if (userHasPerms('user_create', 'gen') || strtolower($userData['role'])):  ?>
                      <button class="btn btn-sm btn-secondary edit-btn" id="fullName-edit">Edit</button>
                    <?php endif; ?>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Mobile Number:</strong> <span id="mobileNum">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" id="mobileNum-edit">Edit</button>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Mobile Number:</strong> <span id="mobileNum">Loading...</span></span>
                    <button class="btn btn-sm btn-secondary edit-btn" id="mobileNum-edit">Edit</button>
                  </li>
                </ul>
                <!-- disabled because the super admin should be the only one to manage accounts -->
                <?php
                if (strtolower($userData['role']) != 'super admin'):
                ?>
                  <p class="text-center"><i class="fas fa-info-circle"></i> Your account can only be deleted by an administrator.</p>
                <?php
                else:
                ?>
                  <p class="text-center"><i class="fas fa-info-circle"></i> Your account can only be deleted by another super admin.</p>
                <?php
                endif;
                ?>
              </div>
            </div>
          </div>
          <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
      </div>
      <!-- End of Page Wrapper -->
</body>

<?php require_once 'components/change_setting_dialog.php' ?>

</html>