<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require_once 'common/auth.php';
if (!userHasPerms('roles_read', 'gen')) {
  header('Location:/mabisa2.0/Admin/no_permissions.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once 'common/head.php';
  ?>
  <script src="../js/roles.js" defer></script>
  <script src="../js/role-change.js" defer></script>
  <script src="../js/util/confirmation.js" defer></script>
  <script src="../js/util/alert.js" defer></script>
  <script src="../js/util/input-validation.js" defer></script>
  <script src="../js/util/phone-prepend.js" defer></script>
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
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Roles</h3>
              </div>
              <div style="float: right;">
                <a href="roles_update.php" class="btn btn-sm btn-primary">Add role</a>
                <a onclick="location.href = 'users.php'" class="btn btn-sm btn-secondary">Go back</a>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-bordered" id="roles_dataTable" width="100%" cellspacing="0">
                  <?php
                  // $stmt = $pdo->prepare("SELECT COUNT(*) FROM pos.received_from where area_code=? and cmp_code=? ");
                  $stmt = $pdo->query("SELECT COUNT(*) FROM roles;");
                  $count = $stmt->fetchColumn();

                  if ($count != 0) {
                  ?>
                    <thead>
                      <tr>
                        <th>Role</th>
                        <th>Global Permissions</th>
                        <th>Barangay Permissions</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <?php if ($count > 10) { ?>
                      <tfoot>
                        <tr>
                          <th>Role</th>
                          <th>Global Permissions</th>
                          <th>Barangay Permissions</th>
                          <th>Actions</th>
                        </tr>
                      </tfoot>
                    <?php } ?>
                    <tbody id='roles-table-body'>
                      <?php
                      $query = $pdo->query("select * from roles");
                      $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                      foreach ($rows as $row) {
                      ?>
                        <tr>
                          <td><?php echo $row['name'] ?></td>
                          <td>
                            <?php
                            require_once '../api/logging.php';
                            $permissionsValue = '';
                            if ($row['gen_perms']) {
                              $query = $pdo->prepare("select * from permissions where id = :gen_perms limit 1");
                              $query->execute([':gen_perms' => $row['gen_perms']]);
                              $permissions = $query->fetch(PDO::FETCH_ASSOC);
                              // remove first item (id)
                              array_shift($permissions);
                              // remove last item (last modified)
                              array_pop($permissions);
                              //get all keys where value is true (or 1 in this case)
                              $keys = array_keys(array_filter($permissions, function ($permission) {
                                return $permission == 1;
                              }));
                              if ($keys) {
                                $permissionsValue = '<ul>';
                                // append to one string
                                foreach ($keys as $key) {
                                  $permissionsValue .= ('<li>' . $key . '</li>');
                                }
                                $permissionsValue .= '<ul>';
                              } else {
                                $permissionsValue = 'No permissions found.';
                              }
                            } else {
                              $permissionsValue = 'No permissions found.';
                            }
                            echo $permissionsValue;
                            ?>
                          </td>
                          <td>
                            <?php
                            $permissionsValue = '';
                            if ($row['bar_perms']) {
                              $query = $pdo->prepare("select * from permissions where id = :bar_perms limit 1");
                              $query->execute([':bar_perms' => $row['bar_perms']]);
                              $permissions = $query->fetch(PDO::FETCH_ASSOC);
                              // remove first item (id)
                              array_shift($permissions);
                              // remove last item (last modified)
                              array_pop($permissions);
                              //get all keys where value is true (or 1 in this case)
                              $keys = array_keys(array_filter($permissions, function ($permission) {
                                return $permission == 1;
                              }));
                              if ($keys) {
                                $permissionsValue = '<ul>';
                                // append to one string
                                foreach ($keys as $key) {
                                  $permissionsValue .= ('<li>' . $key . '</li>');
                                }
                                $permissionsValue .= '<ul>';
                              } else {
                                $permissionsValue = 'No permissions found.';
                              }
                            } else {
                              $permissionsValue = 'No permissions found.';
                            }
                            echo $permissionsValue;
                            ?>
                          </td>
                          <td>
                            <?php
                            if (strtolower($row['name']) !== 'super admin'):
                            ?>
                              <a href="roles_update.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info btn-circle edit-role-btn">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="#delete-role"
                                class="btn btn-sm btn-danger btn-circle delete-role-btn" data-id="<?= $row['id'] ?>" data-gen-perms-id="<?= $row['gen_perms'] ?>" data-bar-perms-id="<?= $row['bar_perms'] ?>">
                                <i class=" fas fa-trash"></i>
                              </a>
                            <?php
                            else:
                            ?>
                              <i class="fas fa-question-circle" style="font-size: 20px" data-toggle="tooltip" data-placement="left" title="Super admin cannot be modified/deleted."></i>
                            <?php
                            endif;
                            ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  <?php } else { ?>
                    <tbody>
                      <tr>
                        <td>
                          <p class="text-center">No roles yet.</p>
                        </td>
                      </tr>
                    </tbody>
                  <?php } ?>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
</body>


</html>