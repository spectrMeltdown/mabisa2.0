<div class="modal fade" id="crud-user" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modalLabel">New User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      <div class="modal-body p-5">
        <!-- loading Spinner-->
        <div id="loadingSpinner" class="d-flex justify-content-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <form id="crud-user-modal-content" class="d-none" data-edit-mode="true" novalidate>
          <!-- for displaying error -->
          <div id="alert"></div>

          <!-- actual content -->
          <div class="row">
            <div class="mb-3 form-group col-lg-6">
              <label for="fullName" class="form-label">Full Name</label>
              <input maxlength="100" type="text" class="form-control" name="fullName" id="fullName" required autocomplete="name" />
              <div class="invalid-feedback">
              </div>
            </div>
            <div class="mb-3 form-group col-lg-6">
              <label for="username" class="form-label">Username</label>
              <input maxlength="100" type="text" class="form-control" name="username" id="username" required autocomplete="username" />
              <div class="invalid-feedback">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="mb-3 form-group col-lg-6">
              <label for="email" class="form-label">Email</label>
              <input maxlength="100" type="email" class="form-control" name="email" id="email" required autocomplete="email" />
              <div class="invalid-feedback">
              </div>
            </div>
            <div class="mb-3 form-group col-lg-6">
              <label for="mobileNum" class="form-label">Mobile Number</label>
              <input title="Please enter a valid phone number." maxLength="10" type="tel" class="form-control" name="mobileNum" id="mobileNum" pattern="^\+?[0-9]*$" inputmode="numeric" required autocomplete="tel" />
              <div class="invalid-feedback">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="mb-3 col-lg-6 form-group" id="passField">
              <label for="pass" class="form-label" id="passwordLabel">Password</label>
              <div class="d-flex">
                <input maxlength="100" type="password" class="form-control" name="pass" id="pass" required autocomplete="new-password" />
                <div class="p-1"></div>
                <button type="button" id="passEye" class="btn btn-outline-secondary d-inline-block">
                  <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
                </button>
              </div>
              <div class="invalid-feedback">
              </div>
            </div>

            <div class="mb-3 col-lg-6 form-group" id="confirmPassField">
              <label for="confirmPass" class="form-label">Confirm password</label>
              <div class="d-flex">
                <input maxlength="100" type="password" class="form-control" name="confirmPass" id="confirmPass" required autocomplete="new-password" />
                <div class="p-1"></div>
                <button type="button" id="confirmPassEye" class="btn btn-outline-secondary">
                  <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
                </button>
              </div>
              <div class="invalid-feedback">
              </div>
            </div>
          </div>

          <div class="mb-3 form-group">
            <label for="role" class="form-label">Role</label>
            <select class="custom-select" name="role" id="role" required>
              <option value="" disabled selected hidden>Select one</option>
              <?php
              // require_once "../models/role_model.php";
              // $options = '';
              // foreach (UserRole::cases() as $role) {
              //   $options .= '<option value="' . htmlspecialchars($role->value) . '">' . htmlspecialchars($role->value) . '</option>';
              // }
              // echo $options;
              ?>
            </select>
            <div class="invalid-feedback">
            </div>
          </div>
          <hr>
          <div class="col" id="auditorRoleAssignment" style="display: none">
            <h5 class="modal-title" id="barangayAssignmentsLabel">Assigned Barangays</h5>
            <div id="barangayAssignmentsLoading">
              <p class="text-align-center mt-2" style="font-size: 14px;">Loading...</p>
              <div class="spinner-border spinner-border-sm mr-2" role="status">
              </div>
            </div>
            <div id="noBarangayAssignments" class="container" style="display: none">
              <p class="text-align-center mt-2" style="font-size: 14px;">No assignments yet.</p>
            </div>
            <ul class="container-fluid" id="barangayAssignmentsList" style="display: none">
            </ul>
            <button type="button" class="btn btn-success btn-sm mt-2" id="barangaySelectBtn" data-toggle="modal" data-target="#barangaySelectorDialog">
              <i class="fas fa-plus-circle"></i>&nbsp;Add
            </button>
            <div class="invalid-feedback">
            </div>
          </div>

          <div class="mb-3 form-group max-w-100 mx-auto" id="barangayDiv" style="display: none;">
            <label for="barangay" class="form-label">Barangay: </label>
            <select class="form-select" name="barangay" id="barangay">
              <option value="" disabled selected hidden>Select one</option>
              <?php
              require_once "../models/barangay_model.php";
              $options = '';
              foreach (Barangay::cases() as $barangay) {
                $options .= '<option value="' . htmlspecialchars($barangay->value) . '">' . htmlspecialchars($barangay->value) . '</option>';
              }
              echo $options;
              ?>
            </select>
            <div class="invalid-feedback">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-user-btn">Save</button>
      </div>
    </div>
  </div>
</div>