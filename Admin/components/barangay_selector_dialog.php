<div class="modal fade" id="barangaySelectorDialog" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white" id="modalLabel">Select Barangays</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- loading Spinner -->
        <div id="barSelectorLoadingSpinner" class="d-none justify-content-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <form id="barangay-select-modal-content" class="needs-validation" novalidate>
          <!-- for displaying error -->
          <div class="mt-4 mb-4">
            <div class="alert alert-danger alert-dismissible fade" role="alert" id="alert">
            </div>
          </div>
          <!-- actual content -->
          <!-- TODO: might migrate to js later, unless un-assignment of the auditor barangays will be done in the main crud dialog-->
          <ul class="list-group" id="barangaySelectList">
            <?php
            require_once '../db/db.php';
            $sql = 'select brgyid, brgyname, auditor from refbarangay where auditor is null';
            $query = $pdo->prepare($sql);
            $query->execute();
            if ($query->fetch(PDO::FETCH_ASSOC)):
              while ($row = $query->fetch(PDO::FETCH_ASSOC)):
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="<?= $row['brgyid'] ?>">
                    <label class="custom-control-label text-grey" for="<?= $row['brgyid'] ?>"><?= $row['brgyname'] ?></label>
                  </div>
                </li>
              <?php
              endwhile;
              ?>
          </ul>
        <?php else:
        ?>
          <h6 id="barSelectNoneText" style="display: none;">Error getting barangays. Try again later.</h6>
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