<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

// use empty to check for all cases (variable unset, blank string, etc). Negation of the variable also works, but may display warning.
if (empty($_COOKIE['id'])) {
  header('location:logged_out.php');
  exit;
}

require_once '../db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require 'common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isCriteriaPhp = true;
    require 'common/sidebar.php' ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <?php require 'common/nav.php' ?>
        <!-- End of Topbar -->
        <!--Header-->
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Area Indicators</h1>
          </div>
          <!-- Begin Page Content -->
          <div class="container-fluid">
            <table id="myTable" class="table table-striped">
              <thead>
                <tr>
                  <th>Governance Code</th>
                  <!-- Header for Governance Code -->
                  <th>Area Number</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                <!-- rows will be added here dynamically -->
              </tbody>
            </table>
            <button id="add-row-btn" class="btn btn-primary">Add</button>
            <button id="save-btn" class="btn btn-success">Save</button>


            <script>
              $(document).ready(function() {
                // Add row button click event
                $("#add-row-btn").on("click", function() {
                  var newRow = "<tr>";
                  newRow +=
                    '<td><select class="form-control"><option>Gov Code 1</option><option>Gov Code 2</option><option>Gov Code 3</option></select></td>'; // Dropdown for Governance Code
                  newRow +=
                    '<td><select class="form-control"><option>Area 1</option><option>Area 2</option><option>Area 3</option></select></td>';
                  newRow +=
                    '<td><input type="text" class="form-control" /></td>';
                  newRow +=
                    '<td><button class="btn btn-danger delete-btn">Delete</button></td>';
                  newRow += "</tr>";
                  $("#table-body").append(newRow);
                });

                // Save button click event
                $("#save-btn").on("click", function() {
                  // TO DO: implement save functionality
                  alert("Save button clicked!");
                });

                // Delete button click event
                $(document).on("click", ".delete-btn", function() {
                  $(this).closest("tr").remove();
                });
              });
            </script>
          </div>
          <!--End Page Content-->
        </div>
      </div>
    </div>
  </div>
</body>

</html>