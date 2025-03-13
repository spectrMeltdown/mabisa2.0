<div class="modal fade" id="changeProfileSettingDialog" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
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
          <div class="alert alert-danger alert-dismissible fade" role="alert" id="alert">
          </div>
          <div class="container">
            <p id="changeProfileSettingSubtitle"></p>
            <div class="mb-3 form-group" id="firstInput">
              <div class="d-flex align-items-center justify-content-center">
                <input maxlength="100" type="text" class="form-control pass" name="newValue" id="newValueInput" required />
                <button
                  type="button"
                  class="btn btn-outline-secondary d-none rounded-circle p-0 passEye"
                  style="width: 60px; height: 50px; margin-left: 5px">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
              <div class="invalid-feedback">
              </div>
            </div>
            <p id="changeProfileSettingSubtitle2"></p>
            <div class="mb-3 form-group" id="secondInput" style="display: none">
              <div class="d-flex align-items-center justify-content-center">
                <input maxlength="100" type="text" class="form-control pass" name="newValue2" id="newValueInput2" required />
                <button
                  type="button"
                  class="btn btn-outline-secondary d-none rounded-circle p-0 passEye"
                  style="width: 60px; height: 50px; margin-left: 5px;">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
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