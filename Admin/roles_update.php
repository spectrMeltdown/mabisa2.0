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
              <form id="crud-role-content" data-edit-mode="true" class="ml-3" novalidate>
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
                <div class="mb-3 form-group container-fluid" id="permissions">
                  <h6 class="mb-3">Permissions</h6>
                  <div id="permissions-alert" class="text-danger"></div>
                  <?php
                  $query = $pdo->query("select * from roles");
                  while ($row = $query->fetch(PDO::FETCH_ASSOC)):
                    require_once '../api/logging.php';
                    $permissionsValue = '';
                    if ($row['permissions_id']):
                      $query = $pdo->prepare("select * from permissions where id = :permissions_id limit 1");
                      $query->execute([':permissions_id' => $row['permissions_id']]);
                      $permissions = $query->fetch(PDO::FETCH_ASSOC);
                      // remove first item (id)
                      array_shift($permissions);
                      // remove last item (last modified)
                      array_pop($permissions);

                      // remove super admin permissions if not super admin
                      if (!($userData['role'] === 'Super Admin')) {
                        $permissions = array_filter($permissions, function ($permission) {
                          return strstr($permission, 'super_admin') === false;
                        });
                      }

                      //get all keys where value is true (or 1 in this case)
                      $keys = array_keys(array_filter($permissions, function ($permission) {
                        return $permission == 1;
                      }));
                  ?>
                      <ul class="list-unstyled">
                        <?php
                        foreach ($keys as $key):
                        ?>
                          <li class="d-inline-block m-1">
                            <div class="input-group mb-3 d-flex flex-row">
                              <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <input type="checkbox" name="<?= $key ?>" id="<?= $key ?>" value="true">
                                </div>
                              </div>
                              <div class="card card-body border-secondary">
                                <label for="<?= $key ?>" id="label-<?= $key ?>"><?= $key ?></label>
                              </div>
                            </div>
                          </li>
                        <?php
                        endforeach;
                        ?>
                      </ul>
                </div>
              <?php
                    else:
              ?>
                <p>No permissions found.</p>
                ?>
            <?php
                    endif;
                  endwhile;
            ?>
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