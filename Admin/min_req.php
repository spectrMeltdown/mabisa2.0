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
  <?php require 'common/head.php' ?>
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
        </div>
      </div>
    </div>
  </div>
</body>

</html>