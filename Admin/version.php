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
          <div class="row">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <h1 class="h3 mb-0 text-gray-800">Criteria Versions</h1>
              <div class="d-sm-flex align-items-center">
                <button class="btn btn-success btn-sm" id="add-row">
                  Add New Criteria Version
                </button>
              </div>
            </div>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">SHORT DEFINITION</th>
                  <th scope="col">DESCRIPTION</th>
                  <th scope="col">ACTIVE YEAR</th>
                  <th scope="col">ACTIVATE</th>
                  <th scope="col">EDIT</th>
                  <th scope="col">DELETE</th>
                </tr>
              </thead>
              <tbody>
                <!-- existing table rows -->
              </tbody>
            </table>
          </div>

          <!-- Modal for Adding New Criteria Version -->
          <div class="modal" tabindex="-1" id="addModal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Criteria Version</h5>
                </div>
                <div class="modal-body">
                  <form id="addForm">
                    <div class="mb-3">
                      <label for="addVersion" class="form-label">Short Definition</label>
                      <input type="text" class="form-control" id="addVersion" required />
                    </div>
                    <div class="mb-3">
                      <label for="addDescription" class="form-label">Description</label>
                      <input type="text" class="form-control" id="addDescription" required />
                    </div>
                    <div class="mb-3">
                      <label for="addYear" class="form-label">Active Year</label>
                      <input type="text" class="form-control" id="addYear" required />
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="addActivate" />
                      <label class="form-check-label" for="addActivate">Activate</label>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="saveAdd">
                    Save changes
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal for Editing -->
          <div class="modal" tabindex="-1" id="editModal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Criteria Version</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="editForm">
                    <div class="mb-3">
                      <label for="editVersion" class="form-label">Short Definiton</label>
                      <input type="text" class="form-control" id="editVersion" required />
                    </div>
                    <div class="mb-3">
                      <label for="editDescription" class="form-label">Description</label>
                      <input type="text" class="form-control" id="editDescription" required />
                    </div>
                    <div class="mb-3">
                      <label for="editYear" class="form-label">Active Year</label>
                      <input type="text" class="form-control" id="editYear" required />
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="editActivate" />
                      <label class="form-check-label" for="editActivate">Activate</label>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                  </button>
                  <button type="button" class="btn btn-primary" id="saveEdit">
                    Save changes
                  </button>
                </div>
              </div>
            </div>
          </div>

          <script>
            document
              .getElementById("add-row")
              .addEventListener("click", () => {
                const modal = new bootstrap.Modal(
                  document.getElementById("addModal")
                );
                modal.show();
              });

            document
              .getElementById("saveAdd")
              .addEventListener("click", () => {
                const tableBody = document.querySelector("tbody");
                const newRow = tableBody.insertRow();
                newRow.innerHTML = `
                                <td>${
                                  document.getElementById("addVersion").value
                                }</td>
                                <td>${
                                  document.getElementById("addDescription")
                                    .value
                                }</td>
                                <td>${
                                  document.getElementById("addYear").value
                                }</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" ${
                                          document.getElementById("addActivate")
                                            .checked
                                            ? "checked"
                                            : ""
                                        }>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                </td>
                            `;
                document
                  .getElementById("addModal")
                  .querySelector("form")
                  .reset();
                const modal = new bootstrap.Modal(
                  document.getElementById("addModal")
                );
                modal.hide();
              });

            document.addEventListener("click", (event) => {
              if (event.target.classList.contains("delete-btn")) {
                const row = event.target.closest("tr");
                row.remove();
              }

              if (event.target.classList.contains("edit-btn")) {
                const row = event.target.closest("tr");
                const version = row.cells[0].textContent;
                const description = row.cells[1].textContent;
                const year = row.cells[2].textContent;
                const activate = row.cells[3].querySelector("input").checked;

                // Populate modal fields
                document.getElementById("editVersion").value = version;
                document.getElementById("editDescription").value =
                  description;
                document.getElementById("editYear").value = year;
                document.getElementById("editActivate").checked = activate;

                // Show the modal
                const modal = new bootstrap.Modal(
                  document.getElementById("editModal")
                );
                modal.show();

                // Save the reference to the row being edited
                document.getElementById("saveEdit").onclick = () => {
                  // Update the row with the edited values
                  row.cells[0].textContent =
                    document.getElementById("editVersion").value;
                  row.cells[1].textContent =
                    document.getElementById("editDescription").value;
                  row.cells[2].textContent =
                    document.getElementById("editYear").value;
                  row.cells[3].querySelector("input").checked =
                    document.getElementById("editActivate").checked;

                  // Hide the modal after saving changes
                  modal.hide();
                };
              }
            });
          </script>
        </div>
      </div>
      <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
  </div>
  <!-- End of Page Wrapper -->
</body>

</html>