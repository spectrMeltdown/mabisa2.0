<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}
require_once '../../db/db.php';


$stmt = $pdo->query("SELECT * FROM maintenance_area");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  
  <?php
  $isInFolder=true;
  require '../common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isCriteriaPhp = true;
    $isInFolder = true;
    require '../common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Assign Area</h1>
          </div>
          <div class="container mt-5" style="padding-bottom: 20px">
            <button type="button" class="btn btn-primary">
              Add Area
            </button>
            
          </div>
          <table class="table table-bordered" style="table-layout: fixed; width: 100%;">
          <thead class="bg-secondary text-white">
                  <tr>
                  <th>Description</th>
        <th>Trail</th>
        <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $row):
              ?>
<tr>
                    
                    <td style="font-size: 20px;"><?php echo $row['description']; ?></td>
                    <td style="font-size: 20px;"><?php echo $row['trail']; ?></td>                   
                    <td>
                      <a class="btn btn-primary open-modal" data-id="<?php echo $row['keyctr']; ?>">
                        Edit
                      </a>
                      <a href="../script.php?delete_id=<?php echo $row['keyctr'] ?>">Delete</a>
                    </td>
                  </tr>
              
              <?php endforeach?> 
                </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</body>

</html>