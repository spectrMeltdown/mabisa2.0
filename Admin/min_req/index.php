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
$stmt = $pdo->query("SELECT * FROM maintenance_area_mininumreqs");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require_once '../common/nav.php' ?>
        <!-- End of Topbar -->
        <!--Header-->
        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Minimum Requirements</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-add-modal"> Create New Requirement</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table table-responsive">
                <table id="maintenanceTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Keyctr</th>
                      <th>Indicator Code</th>
                      <th>Reqs Code</th>
                      <th>Description</th>
                      <th>Sub Minimum Reqs</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data as $row):
                      $stmt = $pdo->prepare("SELECT indicator_code FROM `maintenance_area_indicators` WHERE keyctr = :keyctr");
                      $stmt->bindParam(':keyctr', $row['indicator_keyctr'], PDO::PARAM_INT);
                      $stmt->execute();
                      $indicator_code = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                      <tr>
                        <td><?php echo $row['keyctr']; ?></td>
                        <td><?php echo $indicator_code ? $indicator_code['indicator_code'] : 'N/A'; ?></td>
                        <td><?php echo $row['reqs_code']; ?></td>
                        <td><span class="short-text">
                            <?= htmlspecialchars(substr($row['description'], 0, 200)) . '...'; ?>
                          </span>
                          <span class="full-text" style="display: none;">
                            <?= htmlspecialchars($row['description']); ?>
                          </span>
                          <a href="#" class="see-more">See more</a>
                        </td>
                        <td><?php echo $row['sub_mininumreqs']; ?></td>
                        <td>
                          <a class="btn btn-primary open-modal" data-id="<?php echo $row['keyctr']; ?>">
                            Edit
                          </a>
                          <a href="delete.php?keyctr=<?php echo $row['keyctr']; ?>" class="btn btn-danger delete-btn"
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
  <script>
    $(document).ready(function() {
      $('#open-add-modal').click(function() {
        $('#addMinimumReqModal').modal('show');
      });

      $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm("Are you sure you want to delete this minimum requirement?")) {
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

      if (!keyctr) {
        alert('Error: Missing keyctr!');
        return;
      }

      $.ajax({
        url: 'edit.php',
        type: 'GET',
        data: {
          keyctr: keyctr
        },
        success: function(response) {
          $('body').append(response);
          $('#editMinimumReqModal').modal('show');
        },
        error: function() {
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
  <?php require_once 'add.php' ?>
</body>

</html>