<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

// only user* permissions are allowed
if () {

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
  <script src="../js/users.js" defer></script>
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
    $isUsersPhp = true;
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
                <h3 class="m-0 font-weight-bold text-primary">Users</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-sm btn-primary add-user-btn" href="users_update.php">Add User</a>
                  <a class="btn btn-sm btn-primary ml-2" href="roles.php" data-transition="view">Manage roles</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-bordered" id="user_dataTable" width="100%" cellspacing="0">
                  <?php
                  // $stmt = $pdo->prepare("SELECT COUNT(*) FROM pos.received_from where area_code=? and cmp_code=? ");
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users where id != :id");
                  $stmt->execute([':id' => $userData['id']]);
                  $count = $stmt->fetchColumn();

                  if ($count != 0) {
                  ?>
                    <thead>
                      <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Barangays</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <?php if ($count > 10) { ?>
                      <tfoot>
                        <tr>
                          <th>Full Name</th>
                          <th>Username</th>
                          <th>Role</th>
                          <th>Barangays</th>
                          <th>Actions</th>
                        </tr>
                      </tfoot>
                    <?php } ?>
                    <tbody id='user-table-body'>
                      <?php
                      require_once '../api/get_role_name.php';
                      $query = $pdo->prepare("select * from users where id != :id");
                      // writeLog($userData);
                      $query->execute([':id' => $userData['id']]);
                      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <tr>
                          <td><?php echo $row['full_name'] ?></td>
                          <td><?php echo $row['username'] ?></td>
                          <td><?php echo getRoleName($pdo, $row['role_id']) ?></td>
                          <td><?php
                              $query = $pdo->prepare("SELECT b.brgyname as barangay from refbarangay b inner join user_roles_barangay ub on ub.barangay_id = b.brgyid where ub.user_id = :id");
                              // writeLog($userData);
                              $query->execute([':id' => $userData['id']]);
                              if ($query->rowCount() <= 0) {
                                echo 'No barangay assignments';
                              } else {
                                echo '<ul>';
                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                  echo '<li>' . htmlspecialchars($row['barangay']) . '</li>';
                                }
                                echo '</ul>';
                              }
                              ?></td>
                          <td>
                            <a href="#edit-user" class="btn btn-sm btn-info btn-circle edit-user-btn"
                              data-toggle="modal" data-target="#crud-user" data-id="<?= $row['id'] ?>">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a href="#delete-user"
                              class="btn btn-sm btn-danger btn-circle delete-user-btn" data-id="<?= $row['id'] ?>">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  <?php } else { ?>
                    <tbody>
                      <tr>
                        <td>
                          <p class="text-center">No users yet.</p>
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
  <!-- End of Page Wrapper -->
  <?php require 'components/crud_user_dialog.php' ?>
  <?php require 'components/barangay_selector_dialog.php' ?>
</body>

</html>