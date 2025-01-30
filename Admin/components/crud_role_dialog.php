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
            <div class="mb-3 form-group">
              <label for="roleName" class="form-label">Role name</label>
              <input maxlength="100" type="text" class="form-control" name="roleName" id="roleName" required />
              <div class="invalid-feedback">
              </div>
            </div>
          </div>

          <?php
          require_once '../db/db.php';
          require_once '../api/get_role_name.php';
          global $pdo;
          $query = $pdo->query('select * from roles');
          $roles = $query->fetchAll(PDO::FETCH_ASSOC);
          // If not super admin, then remove option to add a super admin
          if (!($userData['role'] === 'Super Admin')) {
            echo '<script>console.log("not a super admin!")</script>';
            $roles = array_values($roles, function ($role) {
              return $role !== 'Super Admin';
            });
          }
          if (!empty($roles)):
          ?>
            <div class="mb-3 form-group">
              <label for="role" class="form-label">Role</label>
              <select class="custom-select" name="role" id="role" required>
                <option value="" disabled selected hidden>Select one</option>
                <?php
                $options = '';
                foreach ($roles as $role) {
                  $options .= '<option value="' . htmlspecialchars($role['id']) . '">' . htmlspecialchars($role['name']) . '</option>';
                }
                echo $options;
                ?>
              </select>
              <div class="invalid-feedback">
              </div>
            </div>
          <?php else: ?>
            <div class="mb-3">
              <h6>No roles available. Please add some roles first.</h6>
            </div>
          <?php endif; ?>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-user-btn">Save</button>
      </div>
    </div>
  </div>
</div>