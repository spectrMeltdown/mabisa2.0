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

          <?php require 'components/modal.php';
          echo createModal(
            btnTxt: "Add user",
            title: "New User",
            cancelBtnTxt: 'Cancel',
            formAttrs: 'action="../api/create_user.php" method="post"',
            content: '
              <div class="mb-3">
                <label for="" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="fullName" id="fullName" placeholder="e.g John Doe ..." required/>
              </div>
              <div class="mb-3">
                <label for="" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="e.g John Doe ..." required/>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="abc@mail.com" required/>
              </div>
              <div class="mb-3">
                <label for="mobileNum" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" name="mobileNum" id="mobileNum" placeholder="" required/>
              </div>
              <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <input type="password" class="form-control" name="pass" id="pass" placeholder="" required/>
              </div>
              <div class="mb-3">
                <label for="confirmPass" class="form-label">Confirm password</label>
                <input type="password" class="form-control" name="confirmPass" id="confirmPass" placeholder="" required/>
              </div>
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select form-select-lg" name="role" id="role" required>
                  <option value="" disabled selected hidden>Select one</option>
                   ' .
              (function () {
                require_once "../models/role_model.php";
                $options = '';
                foreach (UserRole::cases() as $role) {
                  $options .= '<option value="' . htmlspecialchars($role->value) . '">' . htmlspecialchars($role->toString()) . '</option>';
                }
                return $options;
              })() .
              '</select>
          </div>
          '
          );
          ?>
          <!-- user table -->
          <div class="mx-5">
            <table class="table table-bordered my-3">
              <thead>
                <tr>
                  <!--  Intentionally left blank for checkboxes column -->
                  <!-- <th><input type="checkbox"></th> -->
                  <th>Full Name</th>
                  <th>Role</th>
                  <th>Barangay</th>
                  <!-- <th>Created At</th> -->
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                require '../api/get_users.php';
                /** @var User[] */
                $users = getAllUsers();
                if (!empty($users)):
                  foreach ($users as $user):
                ?>
                    <tr>
                      <!-- <td><input type="checkbox" class="" name="id" id="<?= htmlspecialchars($user->id) ?>"> </td> -->
                      <td><?= htmlspecialchars($user->fullName) ?> </td>
                      <td><?= htmlspecialchars($user->role) ?> </td>
                      <td><?= htmlspecialchars($user->barangay) ?> </td>
                      <td></td>
                    </tr>
                  <?php
                  endforeach;
                else:
                  ?>
                  <tr>
                    <td colspan="4">No users found.</td>
                  </tr>
                <?php endif; ?>
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