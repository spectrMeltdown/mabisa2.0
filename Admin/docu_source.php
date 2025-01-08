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

      </div>
      <!-- End of Main Content -->
    </div>
  </div>
</body>

</html>