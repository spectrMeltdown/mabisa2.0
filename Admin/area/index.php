<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../../db/db.php';

//query for display 
$stmt = $pdo->query("SELECT * FROM maintenance_area");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for success message
session_start();
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']); // Remove message after displaying
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $isInFolder = true;
  require '../common/head.php'; ?>
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <script src="../../js/maintenance-criteria.js"></script>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php
    $isCriteriaPhp = true;
    $isInFolder = true;
    require '../common/sidebar.php'; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php require '../common/nav.php'; ?>
        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h3 class="m-0 font-weight-bold text-primary" style="float: left;">Assign Area</h3>
              <a class="btn btn-primary" id="open-add-modal" style="float: right;">Add Area</a>
            </div>
            <div class="card-body">
              <div class="table table-responsive">
                <table id="maintenanceTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Description</th>
                      <th>Trail</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data as $row): ?>
                      <tr>
                        <td style="font-size: 20px;"><?php echo $row['description']; ?></td>
                        <td style="font-size: 20px;"><?php echo $row['trail']; ?></td>
                        <td>
                          <button class="btn btn-primary edit-btn" data-id="<?php echo $row['keyctr']; ?>">
                            Edit
                          </button>
                          <a href="delete_area.php?keyctr=<?php echo $row['keyctr']; ?>" class="btn btn-danger delete-btn"
                            data-id="<?php echo $row['keyctr']; ?>">Delete</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="editModalContainer"></div>
  <script>
    //open the add modal
    $(document).ready(function () {
      $('#open-add-modal').click(function () {
        $('#addAreaModal').modal('show');
      });

      // Confirmation for delete
      $('.delete-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm("Are you sure you want to delete this area?")) {
          window.location.href = url;
        }
      });

      // Success alert
      var successMessage = "<?php echo $successMessage; ?>";
      if (successMessage) {
        alert(successMessage);
      }
    });
    //edit 
    $(document).on('click', '.edit-btn', function () {
      var keyctr = $(this).data('id');

      if (!keyctr) {
        alert('Error: Missing keyctr!');
        return;
      }

      $.ajax({
        url: 'edit_area.php',
        type: 'GET',
        data: { keyctr: keyctr },
        success: function (response) {
          $('#editModalContainer').html(response);
          $('#editAreaModal').modal('show');
        },
        error: function () {
          alert('Error retrieving data.');
        }
      });
    });

  </script>
  <?php include 'create_area.php'; ?>

</body>

</html>