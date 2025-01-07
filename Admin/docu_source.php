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
    <?php
    $isCriteriaPhp = true;
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
          <div class="row">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <h1 class="h3 mb-0 text-gray-800">Document Sources</h1>
              <div class="d-sm-flex align-items-center justify-content-between mb-4"></div>
            </div>
            <div class="container-fluid">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Document</th>
                    <th scope="col">Source</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="table-body">
                  <!-- Table rows will be added here -->
                </tbody>
              </table>

              <!-- Button to trigger the add row modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRowModal">
                Add Document Source
              </button>

              <!-- Add row modal -->
              <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog" aria-labelledby="addRowModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addRowModalLabel">Add</h5>
                    </div>
                    <div class="modal-body">
                      <!-- Add new row form -->
                      <form id="add-row-form">
                        <div class="form-group">
                          <label for="document">Document:</label>
                          <input type="text" class="form-control" id="document" required />
                          <div id="document-error" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                          <label for="source">Source:</label>
                          <input type="text" class="form-control" id="source" required />
                          <div id="source-error" class="text-danger"></div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                      </button>
                      <button type="button" class="btn btn-primary" id="add-row-btn">
                        Add
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Edit row modal -->
              <div class="modal fade" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editRowModalLabel">
                        Edit Row
                      </h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <!-- Edit row form -->
                      <form id="edit-row-form">
                        <div class="form-group">
                          <label for="edit-document">Document:</label>
                          <input type="text" class="form-control" id="edit-document" required />
                          <div id="edit-document-error" class="text-danger"></div>
                        </div>
                        <div class="form-group">
                          <label for="edit-source">Source:</label>
                          <input type="text" class="form-control" id="edit-source" required />
                          <div id="edit-source-error" class="text-danger"></div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                      </button>
                      <button type="button" class="btn btn-primary" id="save-row-btn">
                        Save Changes
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <script>
              // Initialize variables
              let rows = [];
              let editRowIndex = null;

              // Function to add a new row
              function addRow() {
                const documentName = document
                  .getElementById("document")
                  .value.trim();
                const source = document.getElementById("source").value.trim();

                if (documentName === "" || source === "") {
                  if (documentName === "") {
                    document.getElementById("document-error").innerHTML =
                      "Please enter a document name";
                  } else {
                    document.getElementById("document-error").innerHTML = "";
                  }

                  if (source === "") {
                    document.getElementById("source-error").innerHTML =
                      "Please enter a source";
                  } else {
                    document.getElementById("source-error").innerHTML = "";
                  }
                } else {
                  const newRow = {
                    id: rows.length + 1,
                    document: documentName,
                    source,
                  };
                  rows.push(newRow);
                  renderTable();
                  document.getElementById("add-row-form").reset();
                  $("#addRowModal").modal("hide");
                }
              }

              // Function to edit a row
              function editRow(index) {
                editRowIndex = index;
                const row = rows[index];
                document.getElementById("edit-document").value = row.document;
                document.getElementById("edit-source").value = row.source;
                $("#editRowModal").modal("show");
              }

              // Function to save changes to a row
              function saveChanges() {
                const documentName = document
                  .getElementById("edit-document")
                  .value.trim();
                const source = document
                  .getElementById("edit-source")
                  .value.trim();

                if (documentName === "" || source === "") {
                  if (documentName === "") {
                    document.getElementById("edit-document-error").innerHTML =
                      "Please enter a document name";
                  } else {
                    document.getElementById("edit-document-error").innerHTML =
                      "";
                  }

                  if (source === "") {
                    document.getElementById("edit-source-error").innerHTML =
                      "Please enter a source";
                  } else {
                    document.getElementById("edit-source-error").innerHTML =
                      "";
                  }
                } else {
                  rows[editRowIndex].document = documentName;
                  rows[editRowIndex].source = source;
                  renderTable();
                  $("#editRowModal").modal("hide");
                }
              }

              // Function to cancel editing a row
              function cancelEdit() {
                $("#editRowModal").modal("hide");
              }

              // Function to delete a row
              function deleteRow(index) {
                rows.splice(index, 1);
                renderTable();
              }

              // Function to render the table
              function renderTable() {
                const tableBody = document.getElementById("table-body");
                tableBody.innerHTML = "";
                rows.forEach((row, index) => {
                  const rowElement = document.createElement("tr");
                  rowElement.innerHTML = `
                      <td>${row.id}</td>
                      <td>${row.document}</td>
                      <td>${row.source}</td>
                      <td>
                        <button class="btn btn-primary btn-sm" onclick="editRow(${index})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRow(${index})">Delete</button>
                      </td>
                    `;
                  tableBody.appendChild(rowElement);
                });
              }

              // Add event listeners
              document
                .getElementById("add-row-btn")
                .addEventListener("click", (e) => {
                  e.preventDefault();
                  addRow();
                });

              document
                .getElementById("add-row-form")
                .addEventListener("submit", (e) => {
                  e.preventDefault();
                  addRow();
                });

              document
                .getElementById("save-row-btn")
                .addEventListener("click", (e) => {
                  e.preventDefault();
                  saveChanges();
                });

              document
                .getElementById("cancel-edit")
                .addEventListener("click", cancelEdit);
            </script>
          </div>

          <!-- Content Row -->
          <div class="row"></div>
          <!-- Content Row -->
          <div class="row"></div>

          <!-- Content Row -->
          <div class="row"></div>
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- End of Main Content -->

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
</body>

</html>