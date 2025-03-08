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
$stmt = $pdo->query("SELECT * FROM maintenance_area_mininumreqs_sub");
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
                <h3 class="m-0 font-weight-bold text-primary">Sub Requirements</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-add-modal">Create New Record</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table table-responsive">
                <table id="maintenanceTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>KeyCtr</th>
                      <th>MininumReq KeyCtr</th>
                      <th>Indicator KeyCtr</th>
                      <th>Reqs Code</th>
                      <th>Description</th>
                      <th>Trail</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data as $row):
                    ?>
                      <tr>
                        <td><?= $row['keyctr']; ?></td>
                        <td><?= $row['mininumreq_keyctr']; ?></td>
                        <td><?= $row['indicator_keyctr']; ?></td>
                        <td><?= $row['reqs_code']; ?></td>
                        <td><?= $row['description']; ?></td>
                        <td><?= $row['trail']; ?></td>

                        <td>
                          <a class="btn btn-primary open-modal mb-2" data-id="<?php echo $row['keyctr']; ?>">
                            Edit
                          </a>
                          <a href="delete_minreqsub.php?keyctr=<?php echo $row['keyctr']; ?>" class="btn btn-danger delete-btn"
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
        $('#addMinReqSubModal').modal('show');
      });


      $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm("Are you sure you want to delete this sub requirement?")) {
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
        url: 'edit_minreqsub.php',
        type: 'GET',
        data: {
          keyctr: keyctr
        },
        success: function(response) {
          $('body').append(response);
          $('#editMinReqSubModal').modal('show');
        },
        error: err => {
          console.error(err);
          alert('Error retrieving data.');
        }
      });
    });
  </script>
  <?php include_once 'add_minreqsub.php' ?>
</body>


</html>