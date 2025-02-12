<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require 'common/auth.php';
if (!userHasPerms('map_read', 'gen')) {
  header('Location:no_permissions.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $isLocationPhp = true;
  require 'common/head.php';
  ?>
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
        <?php require 'common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <div class="d-flex flex-column align-items-center text-center">
            <i class="fas fa-wrench mb-5" style="width: 50px; height: 50px; margin-top: 100px"></i>
            <h2 class="text-center">This page is under construction.<br>Please come back later.</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>