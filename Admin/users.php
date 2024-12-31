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
  <script src="own.js"></script>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <?php require 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php include 'common/nav.php'; ?>
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
                  // $stmt = $dbconn->prepare("SELECT COUNT(*) FROM pos.received_from where area_code=? and cmp_code=? ");
                  $stmt = $dbconn->prepare("SELECT COUNT(*) FROM account where account_type!=00");
                  $stmt->execute();
                  $count = $stmt->fetchColumn();

                  if ($count != 0) {
                  ?>
                    <thead>
                      <tr>
                        <th>Id</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <?php if ($count > 10) { ?>
                      <tfoot>
                        <tr>
                          <th>Id</th>
                          <th>Username</th>
                          <th>Password</th>
                          <th>Date</th>
                          <th>Location</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                    <?php } ?>
                    <tbody>
                      <?php
                      // $query = $dbconn->prepare("SELECT * FROM pos.received_from where area_code=? and cmp_code=? order by brand_name");
                      $query = $dbconn->prepare("SELECT a.id,a.username,a.password,a.date,a.account_type,b.country_name,c.region_name,d.province_name,e.city_name,f.barangay_name FROM account as a inner join country as b on a.country_code=b.country_code inner join region as c on a.region_code=c.region_code inner join province as d on a.province_code=d.province_code inner join city as e on a.city_code=e.city_code inner join barangay as f on a.barangay_code=f.barangay_code where a.account_type!=00");
                      // $query->bindParam(1, $area_code);
                      // $query->bindParam(2, $cmp_code);
                      $query->execute();
                      while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                      ?>
                        <tr>
                          <td><?php echo $row['id'] ?></td>
                          <td><?php echo $row['username'] ?></td>
                          <td><?php echo $row['password'] ?></td>
                          <td><?php echo $row['date'] ?></td>
                          <td>
                            <?php echo $row['barangay_name'] . ', ' . $row['city_name'] . ', ' . $row['province_name'] . ', ' . $row['region_name'] . ', ' . $row['country_name'] ?>
                          </td>
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
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <?php include '../lib/footer.php' ?>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <?php include '../lib/bot.php' ?>
  <script src="user.js"></script>

</body>

</html>

<div class="modal fade" id="addLocation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="exampleModalLabel">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mt-4 mb-4">
          <div id="alert"></div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <label for="country">Country Name</label>
              <select class="form-control" id="country" onchange="onchange_country(this.value)">
                <option selected disabled>Select Country</option>
                <?php
                $query = $dbconn->prepare("SELECT * FROM country ");
                $query->execute();
                while ($row1 = $query->fetch(PDO::FETCH_ASSOC)) {
                ?>
                  <option value="<?php echo $row1['country_code'] ?>"><?php echo $row1['country_name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label for="region">Region Name</label>
              <select class="form-control" id="region" onchange="onchange_region(this.value)">
                <option selected disabled>Select Region</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="province">Province Name</label>
              <select class="form-control" id="province" onchange="onchange_province(this.value)">
                <option selected disabled>Select Province</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="city">City/Municipality Name</label>
              <select class="form-control" id="city" onchange="onchange_city(this.value)">
                <option selected disabled>Select City/Municipality</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="barangay">Barangay Name</label>
              <select class="form-control" id="barangay">
                <option selected disabled>Select Barangay</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2">
            <div class="form-group">
              <label for="username_brgy">Username</label>
              <input class="form-control" type="text" name="username_brgy" id="username_brgy">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="password">Password</label>
              <input class="form-control" type="password" name="password" id="password">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="email">Email</label>
              <input class="form-control" type="email" name="email" id="new_email">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input class="form-control" type="text" name="phone" id="phone" placeholder="ex. +639123456789"
                maxlength="13">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="account_type">Account Type</label>
              <select class="form-control" name="account_type" id="account_type">
                <!-- <option value="05">Country's Office</option> -->
                <!-- <option value="04">Region's Office</option> -->
                <!-- <option value="03">Province's Office</option> -->
                <!-- <option value="02">Mayor's Office</option> -->
                <option value="01">Barangay</option>
              </select>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" id="display_edit">


    </div>
  </div>
</div>
<script type="text/javascript">
  $('#user_dataTable').DataTable();
  //   $('#tableLocation').DataTable({
  //     responsive: {
  //     details: {
  //       type: 'column'
  //     }
  //   },
  //   columnDefs: [{
  //     className: 'control',
  //     orderable: false,
  //     targets: 0
  //   }],
  //   order: [1, 'asc']
  // });
</script>