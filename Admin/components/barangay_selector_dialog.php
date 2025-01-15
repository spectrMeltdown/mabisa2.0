<div class="modal fade" id="barangaySelectorDialog" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modalLabel">Select Barangays</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- loading Spinner -->
        <div id="loadingSpinner" class="d-flex justify-content-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <form id="modal-content" class="d-none user-form-submit">
          <!-- for displaying error -->
          <div class="mt-4 mb-4">
            <div id="alert"></div>
          </div>
          <!-- actual content -->
          <ul class="list-group" id="barangayAssignmentsList">
            <?php
            require '../../db/db.php';
            $sql = 'select * from barangay where auditorID = null';
            $query = $pdo->prepare($sql);
            $query->execute();
            if ($query->fetch(PDO::FETCH_ASSOC) != null):
              while ($row = $query->fetch(PDO::FETCH_ASSOC)):
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="<?= $row['id'] ?>">
                    <label class="custom-control-label text-grey" for="<?= $row['id'] ?>"><?= $row['name'] ?></label>
                  </div>
                </li>
              <?php
              endwhile;
            else:
              ?>

            <?php endif; ?>
          </ul>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-user-btn">Save</button>
      </div>
    </div>
  </div>
</div>