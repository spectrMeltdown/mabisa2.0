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
  <script src="../js/own.js"></script>
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
          <!-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p> -->

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h6 class="m-0 font-weight-bold text-primary">Users</h6>
              </div>
              <div style="float: right;">
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addLocation">Add User</button>
              </div>
            </div>
            <div class="card-body" id="viewLocation">
              <div class="table-responsive">
                <table class="table table-bordered" id="user_dataTable" width="100%" cellspacing="0">
                  <?php
                  // $stmt = $pdo->prepare("SELECT COUNT(*) FROM pos.received_from where area_code=? and cmp_code=? ");
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_policy");
                  $stmt->execute();
                  $count = $stmt->fetchColumn();

                  if ($count != 0) {
                  ?>
                    <thead>
                      <tr>
                        <th>Fullname</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Barangay</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <?php if ($count > 10) { ?>
                      <tfoot>
                        <tr>
                          <th>Fullname</th>
                          <th>Username</th>
                          <th>Role</th>
                          <th>Barangay</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                    <?php } ?>
                    <tbody>
                      <?php
                      require_once '../models/role_model.php';
                      $query = $pdo->prepare("select * from user_policy");
                      $query->execute();
                      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <tr>
                          <td><?php echo $row['fullName'] ?></td>
                          <td><?php echo $row['username'] ?></td>
                          <td><?php echo getRole($row['accessLevel'])->toString() ?></td>
                          <td><?php echo $row['barangay'] ?></td>
                          <td>
                            <a href="#" class="btn btn-sm btn-info btn-circle"
                              onclick="edit_user('<?php echo $row['id'] ?>')" data-toggle="modal" data-target="#editUser">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger btn-circle"
                              onclick="delete_user('<?php echo $row['id'] ?>')">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  <?php } else { ?>
                    <tbody>
                      <tr>
                        <td>No Results Found..</td>
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
  <script src="../js/user.js"></script>
</body>

</html>