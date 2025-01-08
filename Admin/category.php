<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
session_set_cookie_params(0);
session_start();

// if (!$_SESSION['id']) {
//   header('location:../actions/logout.php');
// }
require_once '../db/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $isCriteriaPhp = true;
  require 'common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php require 'common/sidebar.php' ?>
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
          <table class="table" id="myTable">
            <thead>
              <tr>
                <th>CODE</th>
                <th>SHORT DEFINITION</th>
                <th>DESCRIPTION</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <!-- rows will be added or deleted dynamically here -->
            </tbody>
          </table>

          <!-- buttons to add, delete, and save rows -->
          <button class="btn btn-primary" id="addRow">Add Row</button>
          <button class="btn btn-success" id="saveRows">Save</button>

          <script>
            // get the table body element
            const tableBody = document.getElementById("tableBody");

            // function to add a new row
            function addRow() {
              const newRow = `
                          <tr>
                            <td><input type="text" placeholder="Core/Essential"></td>
                            <td><input type="text" placeholder="Definition"></td>
                            <td><input type="text" placeholder="Description"></td>
                            <td>
                              <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">Delete</button>
                              <button class="btn btn-primary btn-sm" onclick="editRow(this)">Edit</button>
                            </td>
                          </tr>
                        `;
              tableBody.innerHTML += newRow;
            }

            // function to delete a row
            function deleteRow(btn) {
              const row = btn.parentNode.parentNode;
              tableBody.removeChild(row);
            }

            // function to edit a row (not implemented, you can add your own logic here)
            function editRow(btn) {
              console.log("Edit row functionality not implemented");
            }

            // function to save the added rows
            function saveRows() {
              const rows = tableBody.rows;
              const data = [];
              for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.cells;
                const rowData = {
                  column1: cells[0].getElementsByTagName("input")[0].value,
                  column2: cells[1].getElementsByTagName("input")[0].value,
                  column3: cells[2].getElementsByTagName("input")[0].value,
                };
                data.push(rowData);
              }
              console.log("Saved data:", data);
              // You can send the data to your server or perform any other action here
            }

            // add event listeners to the buttons
            document
              .getElementById("addRow")
              .addEventListener("click", addRow);
            document
              .getElementById("saveRows")
              .addEventListener("click", saveRows);
          </script>

          <!-- Content Row -->
          <div class="row"></div>

          <!-- Content Row -->
          <div class="row"></div>
        </div>

      </div>
      <!-- End of Main Content -->
    </div>
  </div>
</body>

</html>