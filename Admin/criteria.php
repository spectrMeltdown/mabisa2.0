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
  <?php
  $isSetupCriteriaPhp = true;
  require 'common/head.php' ?>
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php
    $isSetupCriteriaPhp = true;
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
            <h1 class="h3 mb-0 text-gray-800">Create Criteria</h1>
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <div class="container mt-5"></div>
            </div>
          </div>
          <!-- Content Row -->
          <div class="dropdown d-inline-block fluid">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
              data-bs-toggle="dropdown" aria-expanded="false" style="width: 200px">
              Version
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 1.0')">Version 1.0</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 2.0')">Version 2.0</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateVersion('Version 3.0')">Version 3.0</a>
              </li>
            </ul>
          </div>
          <input type="text" id="versionInput" class="form-control d-inline-block" style="width: 200px" readonly />

          <script>
            function updateVersion(selectedVersion) {
              document.getElementById("versionInput").value = selectedVersion;
            }
          </script>

          <div class="dropdown d-inline-block fluid">
            <button class="btn btn-primary dropdown-toggle" type="button" id="governanceDropdown"
              data-bs-toggle="dropdown" aria-expanded="false" style="width: 200px">
              Governance
            </button>
            <ul class="dropdown-menu" aria-labelledby="governanceDropdown">
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 1')">Governance 1</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 2')">Governance 2</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 3')">Governance 3</a>
              </li>
              <li>
                <a class="dropdown-item" href="#" onclick="updateGovernance('Governance 4')">Governance 4</a>
              </li>
            </ul>
          </div>
          <input type="text" id="governanceInput" class="form-control d-inline-block" style="width: 200px" readonly />

          <script>
            function updateGovernance(selectedGovernance) {
              document.getElementById("governanceInput").value =
                selectedGovernance;
            }
          </script>

          <!-- Content Row -->
          <script>
            function toggleSubRequirement(select, row) {
              const subRequirementCell = row.querySelector(
                ".sub-requirement-cell"
              );
              if (select.value !== "") {
                // Create a select element for sub-requirements
                subRequirementCell.innerHTML = `
                                    <select class="form-select" placeholder="Select Sub-requirement">
                                        <option value="">No Sub-requirement</option>
                                        <option value="sub-option1">Sub Option 1</option>
                                        <option value="sub-option2">Sub Option 2</option>
                                        <option value="sub-option3">Sub Option 3</option>
                                    </select>`;
              } else {
                subRequirementCell.innerHTML = "";
              }
            }

            function addRow() {
              const table = document.querySelector(".table tbody");
              const newRow = document.createElement("tr");
              newRow.innerHTML = `
                                <td>
                                    <select class="form-select" name="indicatorNew">
                                        <option value="">Select Indicator</option>
                                        <option value="indicator1">Indicator 1</option>
                                        <option value="indicator2">Indicator 2</option>
                                        <option value="indicator3">Indicator 3</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select" name="relevanceNew">
                                        <option value="">Select Relevance/Definition</option>
                                        <option value="relevance1">Relevance 1</option>
                                        <option value="relevance2">Relevance 2</option>
                                        <option value="relevance3">Relevance 3</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select" name="requirementNew" onchange="toggleSubRequirement(this, this.closest('tr'))">
                                        <option value="">No Minimum Requirements</option>
                                        <option value="option1">Option 1</option>
                                        <option value="option2">Option 2</option>
                                        <option value="option3">Option 3</option>
                                    </select>
                                </td>
                                <td class="sub-requirement-cell"></td>
                                <td>
                                    <input type="checkbox" name="movNew" />
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">Delete</button>
                                </td>
                            `;
              table.appendChild(newRow);
            }

            function deleteRow(button) {
              const row = button.closest("tr");
              row.parentNode.removeChild(row);
            }

            document.addEventListener("DOMContentLoaded", function() {
              // Initial setup if needed
            });
          </script>

          <div class="container mt-5">
            <button class="btn btn-success mb-3" onclick="addRow()">
              Add Row
            </button>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>INDICATOR</th>
                  <th>RELEVANCE/DEFINITION</th>
                  <th class="min-requirements">MINIMUM REQUIREMENTS</th>
                  <th>Sub Requirements</th>
                  <th>MOV's</th>
                  <!-- New column header -->
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select class="form-select" name="indicator1">
                      <option value="">Select Indicator</option>
                      <option value="indicator1">Indicator 1</option>
                      <option value="indicator2">Indicator 2</option>
                      <option value="indicator3">Indicator 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="relevance1">
                      <option value="">Select Relevance/Definition</option>
                      <option value="relevance1">Relevance 1</option>
                      <option value="relevance2">Relevance 2</option>
                      <option value="relevance3">Relevance 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="requirement1"
                      onchange="toggleSubRequirement(this, this.closest('tr'))">
                      <option value="">No Minimum Requirements</option>
                      <option value="option1">Option 1</option>
                      <option value="option2">Option 2</option>
                      <option value="option3">Option 3</option>
                    </select>
                  </td>
                  <td class="sub-requirement-cell"></td>
                  <td>
                    <input type="checkbox" name="mov1" />
                  </td>
                  <td>
                    <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">
                      Delete
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <select class="form-select" name="indicator2">
                      <option value="">Select Indicator</option>
                      <option value="indicator1">Indicator 1</option>
                      <option value="indicator2">Indicator 2</option>
                      <option value="indicator3">Indicator 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="relevance2">
                      <option value="">Select Relevance/Definition</option>
                      <option value="relevance1">Relevance 1</option>
                      <option value="relevance2">Relevance 2</option>
                      <option value="relevance3">Relevance 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="requirement2"
                      onchange="toggleSubRequirement(this, this.closest('tr'))">
                      <option value="">No Minimum Requirements</option>
                      <option value="option1">Option 1</option>
                      <option value="option2">Option 2</option>
                      <option value="option3">Option 3</option>
                    </select>
                  </td>
                  <td class="sub-requirement-cell"></td>
                  <td>
                    <input type="checkbox" name="mov2" />
                  </td>
                  <td>
                    <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">
                      Delete
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <select class="form-select" name="indicator3">
                      <option value="">Select Indicator</option>
                      <option value="indicator1">Indicator 1</option>
                      <option value="indicator2">Indicator 2</option>
                      <option value="indicator3">Indicator 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="relevance3">
                      <option value="">Select Relevance/Definition</option>
                      <option value="relevance1">Relevance 1</option>
                      <option value="relevance2">Relevance 2</option>
                      <option value="relevance3">Relevance 3</option>
                    </select>
                  </td>
                  <td>
                    <select class="form-select" name="requirement3"
                      onchange="toggleSubRequirement(this, this.closest('tr'))">
                      <option value="">No Minimum Requirements</option>
                      <option value="option1">Option 1</option>
                      <option value="option2">Option 2</option>
                      <option value="option3">Option 3</option>
                    </select>
                  </td>
                  <td class="sub-requirement-cell"></td>
                  <td>
                    <input type="checkbox" name="mov3" />
                  </td>
                  <td>
                    <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- End of Main Content -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>