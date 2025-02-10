<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../../db/db.php';
$stmt = $pdo->query("SELECT * FROM maintenance_category");
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
  $isInFolder = true;
  require '../common/head.php' ?>
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <script src="../../js/maintenance-criteria.js"></script>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php
    $isCriteriaPhp = true;
    require '../common/sidebar.php' ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php require '../common/nav.php' ?>

        <div class="container-fluid">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div style="float: left;">
                <h3 class="m-0 font-weight-bold text-primary">Category</h3>
              </div>
              <div style="float: right;">
                <div class="row">
                  <a class="btn btn-primary" id="open-add-modal"> Add New Category</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table table-responsive"></div>
              <table id="maintenanceTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Short Definition</th>
                    <th>Description</th>
                    <th>Trail</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data as $row): ?>
                    <tr>
                      <td><?php echo $row['code']; ?></td>
                      <td><?php echo $row['short_def']; ?></td>
                      <td><?php echo $row['description']; ?></td>
                      <td><?php echo $row['trail'] ?></td>
                      <td>
                        <button class="btn btn-primary edit-btn" data-id="<?php echo $row['code']; ?>">Edit</button>
                        <a href="delete_category.php?code=<?php echo $row['code']; ?>" class="btn btn-danger delete-btn"
                          data-id="<?php echo $row['code']; ?>">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Category Modal Container -->
  <div id="editCategoryModalContainer"></div>

  <script>
    $(document).ready(function () {
      $('#open-add-modal').click(function () {
        $('#addCategory').modal('show');
      });

      $('.delete-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (confirm("Are you sure you want to delete this category?")) {
          window.location.href = url;
        }
      });

      var successMessage = "<?php echo $successMessage; ?>";
      if (successMessage) {
        alert(successMessage);
      }

      $(document).on('click', '.edit-btn', function () {
        var code = $(this).data('id');

        if (!code) {
          alert('Error: Missing category code!');
          return;
        }

        $.ajax({
          url: 'edit_category.php',
          type: 'GET',
          data: { code: code },
          success: function (response) {
            $('#editCategoryModalContainer').html(response);
            $('#editCategoryModal').modal('show');
          },
          error: function () {
            alert('Error retrieving category data.');
          }
        });
      });
    });
  </script>

  <?php include 'create_category.php' ?>
</body>

</html>
