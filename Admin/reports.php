<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

require 'common/auth.php';
require_once '../db/db.php';
require_once '../Admin/bar_assessment/responses.php';
if (!userHasPerms('reports_read', 'gen')) {
  header('Location:no_permissions.php');
  exit;
}

$response = new Responses($pdo);

$barangay = $response->show_responses();

$topBarangays = array_slice($barangay, 0, 3);

foreach ($topBarangays as $data) {
  $datas = $response->getProgress($data['barangay_id']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'common/head.php';
  ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isReports = true;
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
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">
                Top Performing Barangays
              </h6>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush">
                <ul class="list-group list-group-flush">
                  <?php
                  foreach ($topBarangays as $data) {
                    $stmt = 'SELECT brgyname FROM refbarangay WHERE brgyid = :brgyid';
                    $stmt2 = $pdo->prepare($stmt);
                    $stmt2->bindParam(':brgyid', $data['barangay_id'], PDO::PARAM_INT);
                    $stmt2->execute();
                    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    $progress = $response->getProgress($data['barangay_id']);

                    if ($result2) {
                      echo '<li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 25px;">
                    ' . htmlspecialchars($result2['brgyname']) . ' 
                    <span class="badge badge-primary badge-pill">' . $progress . '%</span>
                  </li>';
                    } else {
                      echo '<li class="list-group-item text-muted">No name found</li>';
                    }
                  }
                  ?>
                </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</body>

</html>