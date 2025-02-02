<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../../db/db.php';
$stmt = $pdo->query("SELECT a.*, b.cat_code 
                          FROM maintenance_area_indicators a 
                          INNER JOIN maintenance_governance b ON a.governance_code = b.keyctr");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php 
  $isInFolder = true;
  require '../common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isCriteriaPhp = true;
    require '../common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!--Header-->
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Area Indicator</h1>
          </div>
          <!-- Begin Page Content -->
          <div class="container mt-5" style="padding-bottom: 20px">
            <button type="button" class="btn btn-primary">
              Add New Indicator Key
            </button>
          </div>
          <table class="table table-bordered">
            <thead class="bg-secondary text-white">
              <tr>
              <th>KeyCtr</th>
        <th>Governance Code</th>
        <th>Description Key</th>
        <th>Area Description</th>
        <th>Indicator Code</th>
        <th>Indicator Description</th>
        <th>Relevance Definition</th>
        <th>Minimum Requirement</th>
        <th>Trail</th>
        <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $row):
                ?>
                <tr>
                  <td><?php echo $row['keyctr']; ?></td>
                  <td><?php echo $row['cat_code']; ?></td>
                  <td><?php echo $row['desc_keyctr']; ?></td>
                  <td><?php echo $row['area_description']; ?></td>
                  <td><?php echo $row['indicator_code']; ?></td>
                  <td><?php echo $row['indicator_description']; ?></td>
                  <td><?php echo $row['relevance_def']; ?></td>
                  <td><?php echo $row['min_requirement']; ?></td>
                  <td><?php echo $row['trail']; ?></td>
                  <td>
                    <a class="btn btn-primary open-modal" data-id="<?php echo $row['keyctr']; ?>">
                      Edit
                    </a>
                    <a href="../script.php?delete_id=<?php echo $row['keyctr'] ?>">Delete</a>
                  </td>
                </tr>

              <?php endforeach ?>
            </tbody>
          </table>
          <!--End Page Content-->
        </div>
      </div>
    </div>
  </div>
</body>


</html>