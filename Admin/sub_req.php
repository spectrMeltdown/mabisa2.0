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
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isCriteriaPhp = true;
    require 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require 'common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Page Heading -->
        <div class="container-fluid">
          <div class="row">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <h1 class="h3 mb-0 text-gray-800">Sub Requirements</h1>
              <div class="d-sm-flex align-items-center justify-content-between mb-4"></div>
            </div>
          </div>
          <!-- Begin Page Content -->
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                      Sub Requirements Table
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                          <tr>
                            <th>Minimum Requirements</th>
                            <th>Sub Requirements Code</th>
                            <th>Sub Requirements Description</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              <select class="form-control">
                                <option>Requirement 1</option>
                                <option>Requirement 2</option>
                                <option>Requirement 3</option>
                              </select>
                            </td>
                            <td>
                              <select class="form-control">
                                <option>SR001</option>
                                <option>SR002</option>
                                <option>SR003</option>
                              </select>
                            </td>
                            <td>Description for Sub Requirement 1</td>
                          </tr>
                          <tr>
                            <td>
                              <select class="form-control">
                                <option>Requirement 1</option>
                                <option>Requirement 2</option>
                                <option>Requirement 3</option>
                              </select>
                            </td>
                            <td>
                              <select class="form-control">
                                <option>SR001</option>
                                <option>SR002</option>
                                <option>SR003</option>
                              </select>
                            </td>
                            <td>Description for Sub Requirement 2</td>
                          </tr>
                          <tr>
                            <td>
                              <select class="form-control">
                                <option>Requirement 1</option>
                                <option>Requirement 2</option>
                                <option>Requirement 3</option>
                              </select>
                            </td>
                            <td>
                              <select class="form-control">
                                <option>SR001</option>
                                <option>SR002</option>
                                <option>SR003</option>
                              </select>
                            </td>
                            <td>Description for Sub Requirement 3</td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End of Main Content -->
    </div>
  </div>
</body>

</html>