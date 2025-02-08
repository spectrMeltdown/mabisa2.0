<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../../db/db.php';
$stmt = $pdo->query("SELECT * FROM maintenance_governance");
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
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Governance Assignment</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-add-modal"> Add New Governance Entry</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table table-responsive"></div>
              <table class="table table-bordered">
               <thead>
                  <tr>
                    <th>ID</th>
                    <th>Category Code</th>
                    <th>Area Keyctr</th>
                    <th>Description Keyctr</th>
                    <th>Description</th>
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
                      <td><?php echo $row['area_keyctr']; ?></td>
                      <td><?php echo $row['desc_keyctr']; ?></td>
                      <td><?php echo $row['description']; ?></td>
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
    </div>
  </div>
  </div>
</body>

</html>