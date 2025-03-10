<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

$isInFolder = true;
require_once '../common/auth.php';
if (!userHasPerms('criteria_read', 'gen')) {
  // header does not allow relative paths, so this is my temporary solution
  header('Location:' .  substr(__DIR__, 0, strrpos(__DIR__, '/')) . 'no_permissions.php');
  exit;
}

require_once '../../db/db.php';
$stmt = $pdo->query("SELECT a.*, b.cat_code 
                          FROM maintenance_area_indicators a 
                          INNER JOIN maintenance_governance b ON a.governance_code = b.keyctr");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for success message
session_start();
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require_once '../common/head.php' ?>
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <script src="../../js/maintenance-criteria.js"></script>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isCriteriaPhp = true;
    require_once '../common/sidebar.php' ?>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <div id="content">
        <!-- Topbar -->
        <?php require_once '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!--Header-->
        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Area Indicator</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-add-modal">Add New Indicator Key</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table table-responsive">
                <table id="maintenanceTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Governance Code</th>

                      <th>Area Description</th>
                      <th>Indicator Code</th>
                      <th>Indicator Description</th>
                      <th>Trail</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data as $row):
                    ?>
                      <tr>
                        <td><?php echo $row['cat_code']; ?></td>
                        <td><?php echo $row['area_description']; ?></td>
                        <td><?php echo $row['indicator_code']; ?></td>
                        <td><?php echo $row['indicator_description']; ?></td>

                        <td><?php echo $row['trail']; ?></td>
                        <td>
                          <a class="btn btn-primary open-modal mb-2" data-id="<?php echo $row['keyctr']; ?>">
                            Edit
                          </a>
                          <a href="delete_indicators.php?keyctr=<?php echo $row['keyctr']; ?>" class="btn btn-danger delete-btn"
                            data-id="<?php echo $row['keyctr']; ?>">Delete</a>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!--End Page Content-->
        </div>
      </div>
    </div>
  </div>
  <div id="editModalContainer"></div>
  <script>
    $(document).ready(function() {
      $('#open-add-modal').click(function() {
        $('#addIndicatorModal').modal('show');
      });

      // Attach event handler using 'on'
      $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm("Are you sure you want to delete this indicator?")) {
          window.location.href = url;
        }
      });

      var successMessage = "<?php echo $successMessage; ?>";
      if (successMessage) {
        alert(successMessage);
      }
    });

    $(document).on('click', '.open-modal', function() {
      var keyctr = $(this).data('id');
      $.ajax({
        url: 'edit_indicators.php',
        type: 'GET',
        data: {
          keyctr: keyctr
        },
        success: function(response) {
          $('#editModalContainer').html(response);
          $('#editIndicatorModal').modal('show');
        },
        error: err => {
          console.error(err);
          alert('Error retrieving data.');
        }
      });
    });

    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".see-more").forEach(function(link) {
        link.addEventListener("click", function(event) {
          event.preventDefault();
          let parent = this.parentElement;
          parent.querySelector(".short-text").style.display = "none";
          parent.querySelector(".full-text").style.display = "inline";
          this.style.display = "none";
        });
      });
    });
  </script>

  <?php include_once 'create_indicators.php' ?>
</body>

</html>