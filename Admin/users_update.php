<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}
// require '../api/logging.php';
// if (roleAccess()) {
// }

require_once '../db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'common/head.php' ?>
  <script src="../js/util/confirmation.js" defer></script>
  <script src="../js/util/input-validation.js" defer></script>
  <script src="../js/util/phone-prepend.js" defer></script>
  <script src="../js/users-update.js" defer></script>
  <style>
    /* bigger checkboxes */
    input[type=checkbox] {
      transform: scale(1.2, 1.2);
      /* Double-sized Checkboxes */
      -ms-transform: scale(1.2);
      /* IE */
      -moz-transform: scale(1, 2);
      /* FF */
      -webkit-transform: scale(1.2);
      /* Safari and Chrome */
      -o-transform: scale(1.2);
      /* Opera */
      padding: 10px;
    }
  </style>
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
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">
                  <?php
                  if (empty($_GET['id'])):
                  ?>
                    Add
                  <?php
                  else:
                  ?>
                    Edit
                  <?php
                  endif;
                  ?>
                  User
                </h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <button class="btn btn-sm btn-primary" id="save-user-btn">Save</button>
                  <button class="btn btn-sm btn-secondary ml-2" id="cancel-btn">Cancel</button>
                </div>
              </div>
            </div>
            <div class="card-body">
              <!-- loading Spinner-->
              <!-- <div id="loadingSpinner" class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              </div> -->
              <form id="user-details-form" class="ml-3" novalidate>
                <!-- for displaying error -->
                <div id="alert"></div>

                <br>
                <h3 class="mb-3">Account details</h3>
                <hr>

                <!-- actual content -->
                <div class="row">
                  <div class="mb-3 form-group col-lg-6">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input maxlength="100" type="text" class="form-control" name="fullName" id="fullName" required autocomplete="name" />
                    <div class="invalid-feedback">
                    </div>
                  </div>
                  <div class="mb-3 form-group col-lg-6">
                    <label for="username" class="form-label">Username</label>
                    <input maxlength="100" type="text" class="form-control" name="username" id="username" required autocomplete="username" />
                    <div class="invalid-feedback">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-3 form-group col-lg-6">
                    <label for="email" class="form-label">Email</label>
                    <input maxlength="100" type="email" class="form-control" name="email" id="email" required autocomplete="email" />
                    <div class="invalid-feedback">
                    </div>
                  </div>
                  <div class="mb-3 form-group col-lg-6">
                    <label for="mobileNum" class="form-label">Mobile Number</label>
                    <input title="Please enter a valid phone number." maxLength="10" type="tel" class="form-control" name="mobileNum" id="mobileNum" pattern="^\+?[0-9]*$" inputmode="numeric" required autocomplete="tel" />
                    <div class="invalid-feedback">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="mb-3 col-lg-6 form-group" id="passField">
                    <label for="pass" class="form-label" id="passwordLabel">Password</label>
                    <div class="d-flex">
                      <input maxlength="100" type="password" class="form-control" name="pass" id="pass" required autocomplete="new-password" />
                      <div class="p-1"></div>
                      <button type="button" id="passEye" class="btn btn-outline-secondary d-inline-block">
                        <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
                      </button>
                    </div>
                    <div class="invalid-feedback">
                    </div>
                  </div>

                  <div class="mb-3 col-lg-6 form-group" id="confirmPassField">
                    <label for="confirmPass" class="form-label">Confirm password</label>
                    <div class="d-flex">
                      <input maxlength="100" type="password" class="form-control" name="confirmPass" id="confirmPass" required autocomplete="new-password" />
                      <div class="p-1"></div>
                      <button type="button" id="confirmPassEye" class="btn btn-outline-secondary">
                        <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
                      </button>
                    </div>
                    <div class="invalid-feedback">
                    </div>
                  </div>
                </div>
              </form>
              <form id="user-roles-form" class="ml-3" novalidate>
                <!-- Roles selector -->
                <?php
                require_once '../db/db.php';
                require_once '../api/get_role_name.php';
                global $pdo;
                $query = $pdo->query('select * from roles');
                $roles = $query->fetchAll(PDO::FETCH_ASSOC);
                // If not super admin, then remove option to add a super admin
                if (!($userData['role'] === 'Super Admin')) {
                  // echo '<script>console.log("not a super admin!")</script>';
                  $roles = array_values($roles, function ($role) {
                    return $role !== 'Super Admin';
                  });
                }
                if (!empty($roles)):
                ?>
                  <div class="mb-3 form-group">
                    <label for="roleSelect" class="form-label">Role</label>
                    <select class="custom-select" name="role" id="roleSelect" required>
                      <option value="" disabled selected hidden>Select one</option>
                      <?php
                      $options = '';
                      foreach ($roles as $role) {
                        $options .= '<option value="' . htmlspecialchars($role['id']) . '">' . htmlspecialchars($role['name']) . '</option>';
                      }
                      echo $options;
                      ?>
                    </select>
                    <div class="invalid-feedback">
                    </div>
                  </div>
                <?php else: ?>
                  <div class="mb-3">
                    <h6>No roles available.</h6>
                  </div>
                <?php endif; ?>

                <br>
                <h3 class="mb-3">User permissions</h3>
                <hr>

                <h6 class="mb-3" id="noRoleSelected">Select a role to choose permissions.</h6>

                <!-- General permissions selector, if has barangays-->
                <div class="mb-3 form-group container-fluid" id="genPermContainer" style="display: none">
                  <h4 class="mb-3"><strong>Global scope</strong></h4>
                  <div id="genPermAlert" class="text-danger"></div>
                  <p id="genPermNoPerm" style="display: none;">No general permissions available.</p>
                  <ul id="genPermList" class="list-unstyled"></ul>
                </div>
                <br>
                <hr>

                <!-- Per-barangay permission, if allowed -->
                <div class="mb-3 form-group container-fluid" id="barPermContainer" style="display: none;">
                  <h4 class="mb-3"><strong>Barangay scope</strong></h4>
                  <div id="barPermAlert" class="text-danger"></div>
                  <div class="table-responsive">
                    <table class="table table-bordered" id="barPermTable" width="100%" cellspacing="0">
                    </table>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i> Scroll to Top
  </a>
</body>

</html>