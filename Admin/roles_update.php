<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require_once 'common/auth.php';
if (!(userHasPerms('roles_create', 'gen') || userHasPerms('roles_update', 'gen'))) {
  header('Location:no_permissions.php');
  exit;
}

// for general perms and role perms
require_once '../api/get_all_perm_cols.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'common/head.php';
  ?>
  <script src="../js/role-change.js" defer></script>
  <script src="../js/util/confirmation.js" defer></script>
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
    require_once 'common/sidebar.php' ?>
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
                  Role
                </h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <button class="btn btn-sm btn-primary" id="save-role-btn">Save</button>
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
              <form id="role-form" class="ml-3" novalidate>
                <!-- for displaying error -->
                <div class="alert alert-danger alert-dismissible fade" role="alert" id="alert">
                </div>
                <!-- actual content -->
                <div class="mb-3 form-group">
                  <label for="roleName" class="form-label">Role name</label>
                  <input maxlength="100" type="text" class="form-control" name="roleName" id="roleName" required />
                  <div class="invalid-feedback">
                  </div>
                </div>
                <div class="mb-3 form-group">
                  <input type="checkbox" name="allowBarangay" id="allowBarangay" value="true">
                  <div class="mr-1 d-inline-block"></div>
                  <label for="allowBarangay" class="form-label">Allow assigning of barangays?</label>
                  <i class="fas fa-question-circle" style="font-size: 15px" data-toggle="tooltip" data-placement="left" title="Barangay assignments can be done when creating or editing users."></i>
                </div>
              </form>
              <form id="bar-perms-form" class="ml-3" style="display: none" novalidate>
                <hr>
                <br>
                <h6 class="mb-3">Barangay Scope Permissions</h6>
                <div class="mb-3 form-group container-fluid" id="permissions">
                  <div id="bar-permissions-alert" class="text-danger"></div>
                  <?php
                  $barPermNames = getPermTableNames($pdo, 'assessment');
                  ?>
                  <ul class="list-unstyled">
                    <?php
                    // loop permissions
                    foreach ($barPermNames as $perm):
                    ?>
                      <li class="d-inline-block m-1">
                        <div class="input-group mb-3 d-flex flex-row">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <input type="checkbox" name="<?= $perm ?>" id="bar-<?= $perm ?>" value="true">
                            </div>
                          </div>
                          <div class="card card-body border-secondary">
                            <label for="bar-<?= $perm ?>" id="label-bar-<?= $perm ?>"><?= $perm ?></label>
                          </div>
                        </div>
                      </li>
                    <?php
                    endforeach;
                    ?>
                  </ul>
                </div>
              </form>
              <hr>
              <br>
              <form id="gen-perms-form" class="ml-3" novalidate>
                <h6 class="mb-3">Global Scope Permissions</h6>
                <div class="mb-3 form-group container-fluid" id="gen-permissions">
                  <div id="gen-permissions-alert" class="text-danger"></div>
                  <?php
                  $genPermNames = getPermTableNames($pdo);

                  // remove super admin permissions if not super admin
                  if (!(strtolower($userData['role']) === 'super admin')) {
                    $genPermNames = array_filter($genPermNames, function ($permission) {
                      return !str_contains($permission, 'super_admin');
                    });
                  }
                  ?>
                  <ul class="list-unstyled">
                    <?php
                    // loop permissions
                    foreach ($genPermNames as $perm):
                    ?>
                      <li class="d-inline-block m-1 perm-container" id="<?= $perm ?>-container">
                        <div class="input-group mb-3 d-flex flex-row">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              <input type="checkbox" name="<?= $perm ?>" id="<?= $perm ?>" value="true">
                            </div>
                          </div>
                          <div class="card card-body border-secondary">
                            <label for="<?= $perm ?>" id="label-<?= $perm ?>"><?= $perm ?></label>
                          </div>
                        </div>
                      </li>
                    <?php
                    endforeach;
                    ?>
                  </ul>
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