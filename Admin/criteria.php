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
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <div class="header-title">
            MABILISANG AKSYON INFORMATION SYSTEM OF ALORAN
          </div>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <li>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                      aria-label="Search" aria-describedby="basic-addon2" />
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">3+</span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Alerts Center</h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 12, 2019</div>
                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-success">
                      <i class="fas fa-donate text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 7, 2019</div>
                    $290.29 has been deposited into your account!
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for
                    your account.
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
              </div>
            </li>

            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Message Center</h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="../img/undraw_profile_1.svg" alt="..." />
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">
                      Hi there! I am wondering if you can help me with a
                      problem I've been having.
                    </div>
                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="../img/undraw_profile_2.svg" alt="..." />
                    <div class="status-indicator"></div>
                  </div>
                  <div>
                    <div class="text-truncate">
                      I have the photos that you ordered last month, how would
                      you like them sent to you?
                    </div>
                    <div class="small text-gray-500">Jae Chun · 1d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="../img/undraw_profile_3.svg" alt="..." />
                    <div class="status-indicator bg-warning"></div>
                  </div>
                  <div>
                    <div class="text-truncate">
                      Last month's report looks great, I am very happy with
                      the progress so far, keep up the good work!
                    </div>
                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="..." />
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div>
                    <div class="text-truncate">
                      Am I a good boy? The reason I ask is because someone
                      told me that people say this to all dogs, even if they
                      aren't good...
                    </div>
                    <div class="small text-gray-500">
                      Chicken the Dog · 2w
                    </div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg" />
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../index_main.php" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
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
<!-- Bootstrap core JavaScript-->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="../js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="../vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="../js/demo/chart-area-demo.js"></script>
<script src="../js/demo/chart-pie-demo.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

</html>