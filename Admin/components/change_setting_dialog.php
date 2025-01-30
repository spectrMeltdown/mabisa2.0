<div class="modal fade" id="changeProfileSettingDialog" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="changeProfileSettingTitle"></h5>
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

        <form id="settingFormData" class="needs-validation" novalidate>
          <!-- for displaying error -->
          <div class="mt-4 mb-4">
            <div id="alert"></div>
          </div>
          <div class="container">
            <p id="changeProfileSettingSubtitle"></p>
            <div class="mb-3 form-group">
              <input maxlength="100" type="text" class="form-control" name="newValue" id="newValue" required />
              <div class="invalid-feedback">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="changeProfileSettingSubmit">Save</button>
      </div>
    </div>
  </div>
</div>