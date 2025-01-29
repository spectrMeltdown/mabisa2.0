<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
// ensure the user is still logged in, redirect if not
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
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Minimum Requirements</h1>
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <div class="container mt-5"></div>
            </div>
          </div>
          <!-- Content Row -->

          <!-- Content Row -->
          <div class="container-fluid">
            <button class="btn btn-primary mb-3" id="addRowButton">
              Add New Row
            </button>
            <button class="btn btn-success mb-3" id="saveButton">Save</button>
            <table class="table table-bordered" id="requirementsTable">
              <thead>
                <tr>
                  <th>Indicator</th>
                  <th>Min Requirements Code</th>
                  <th>Description</th>
                  <th>Sub Requirements</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="table-body">
                <tr>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-light dropdown-toggle form-control border" type="button"
                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Select Indicator
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li>
                          <a class="dropdown-item" href="#" data-value="Indicator 1">Indicator 1</a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" data-value="Indicator 2">Indicator 2</a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" data-value="Indicator 3">Indicator 3</a>
                        </li>
                      </ul>
                    </div>
                  </td>
                  <td>
                    <input type="text" class="form-control" placeholder="Enter Min Requirements Code" />
                  </td>
                  <td>
                    <input type="text" class="form-control" placeholder="Enter Description" />
                  </td>
                  <td>
                    <input type="number" class="form-control narrow-input"
                      placeholder="Enter number of Sub Requirements" min="0" />
                  </td>
                  <td>
                    <button class="btn btn-danger deleteRowButton">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

          <script>
            const addRowButton = document.getElementById("addRowButton");
            const saveButton = document.getElementById("saveButton");
            const tableBody = document.getElementById("table-body");

            addRowButton.addEventListener("click", function() {
              const newRow = document.createElement("tr");
              newRow.innerHTML = `
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-light dropdown-toggle form-control border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Select Indicator
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" data-value="Indicator 1">Indicator 1</a></li>
                                                    <li><a class="dropdown-item" href="#" data-value="Indicator 2">Indicator 2</a></li>
                                                    <li><a class="dropdown-item" href="#" data-value="Indicator 3">Indicator 3</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td><input type="text" class="form-control" placeholder="Enter Min Requirements Code"></td>
                                        <td><input type="text" class="form-control" placeholder="Enter Description"></td>
                                        <td><input type="number" class="form-control narrow-input" placeholder="Enter number of Sub Requirements" min="0"></td>
                                        <td><button class="btn btn-danger deleteRowButton">Delete</button></td>
                                    `;
              tableBody.appendChild(newRow);

              const deleteRowButton =
                newRow.querySelector(".deleteRowButton");
              deleteRowButton.addEventListener("click", function() {
                tableBody.removeChild(newRow);
              });
            });

            tableBody.addEventListener("click", function(event) {
              if (event.target.classList.contains("deleteRowButton")) {
                const row = event.target.closest("tr");
                tableBody.removeChild(row);
              } else if (event.target.classList.contains("dropdown-item")) {
                event.preventDefault();
                const selectedValue = event.target.getAttribute("data-value");
                const dropdownButton = event.target
                  .closest(".dropdown")
                  .querySelector(".dropdown-toggle");
                dropdownButton.innerText = selectedValue;
              }
            });

            saveButton.addEventListener("click", function() {
              const rows = tableBody.children;
              const data = [];

              for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const indicator =
                  row.querySelector(".dropdown-toggle").innerText;
                const minRequirementsCode = row.querySelector(
                  'input[placeholder="Enter Min Requirements Code"]'
                ).value;
                const description = row.querySelector(
                  'input[placeholder="Enter Description"]'
                ).value;
                const subRequirements = row.querySelector(
                  'input[placeholder="Enter number of Sub Requirements"]'
                ).value;

                data.push({
                  indicator: indicator,
                  minRequirementsCode: minRequirementsCode,
                  description: description,
                  subRequirements: subRequirements,
                });
              }

              console.log(data);
              alert(
                "Data saved successfully! Check the console for details."
              );
            });
          </script>
          <!-- End of Content Row -->
          <!-- End of Page Content -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>