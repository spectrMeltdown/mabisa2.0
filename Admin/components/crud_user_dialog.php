<div class="modal fade" id="crud-user" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modalLabel">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- loading Spinner (hidden initially) -->
        <div id="loadingSpinner" class="d-flex justify-content-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <form id="modal-content" class="d-none">
          <!-- for displaying error -->
          <div class="mt-4 mb-4">
            <div id="alert"></div>
          </div>

          <!-- actual content -->
          <div class="row">
            <div class="mb-3 form-group col-lg-6">
              <label for="fullName" class="form-label">Full Name</label>
              <input max="100" type="text" class="form-control" name="fullName" id="fullName" required autocomplete="name" />
            </div>
            <div class="mb-3 form-group col-lg-6">
              <label for="username" class="form-label">Username</label>
              <input max="100" type="text" class="form-control" name="username" id="username" required autocomplete="username" />
            </div>
          </div>

          <div class="row">
            <div class="mb-3 form-group col-lg-6">
              <label for="email" class="form-label">Email</label>
              <input max="100" type="email" class="form-control" name="email" id="email" required autocomplete="email" />
            </div>
            <div class="mb-3 form-group col-lg-6">
              <label for="mobileNum" class="form-label">Mobile Number</label>
              <input title="Please enter a valid phone number." maxLength="13" maxLength="11" type="tel" class="form-control" name="mobileNum" id="mobileNum" pattern="^\+?[0-9]*$" inputmode="numeric" required autocomplete="tel" />
            </div>
          </div>

          <div class="mb-3 col-lg-6 form-group">
            <label for="pass" class="form-label" id="passwordLabel">Password</label>
            <div class="d-flex">
              <input max="100" type="password" class="form-control" name="pass" id="pass" required autocomplete="new-password" />
              <button type="button" id="passEye" class="btn btn-outline-secondary d-inline-block">
                <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
              </button>
            </div>
          </div>

          <div class="mb-3 col-lg-6 form-group">
            <label for="confirmPass" class="form-label">Confirm password</label>
            <div class="d-flex">
              <input max="100" type="password" class="form-control" name="confirmPass" id="confirmPass" required autocomplete="new-password" />
              <button type="button" id="confirmPassEye" class="btn btn-outline-secondary">
                <i class="fa fa-eye"></i> <!-- Add Font Awesome for the icon -->
              </button>
            </div>
          </div>

          <div class="mb-3 form-group">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" name="role" id="role" required>
              <option value="" disabled selected hidden>Select one</option>
              <?php
              require_once "../models/role_model.php";
              $options = '';
              foreach (UserRole::cases() as $role) {
                $options .= '<option value="' . htmlspecialchars($role->value) . '">' . htmlspecialchars($role->toString()) . '</option>';
              }
              echo $options;
              ?>
            </select>
          </div>

          <div class="mb-3 form-group max-w-100 mx-auto" id="barangayDiv" style="display: none;">
            <label for="barangay" class="form-label">Barangay</label>
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
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
      </div>
    </div>
  </div>
</div>