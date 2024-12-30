<?php

function createModal(string $btnTxt, string $title, string $content, string $cancelBtnTxt = 'Close'): string
{
  return '
   <!-- Button to open the modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                ' . $btnTxt . '
              </button>

              <!-- The Modal -->
              <div class="modal fade" id="myModal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                      <h4 class="modal-title">' . $title . '</h4>
                      <button type="button" class="close" data-dismiss="modal">
                        &times;
                      </button>
                    </div>

                    <div class="modal-body">
                      ' . $content . '
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        ' . $cancelBtnTxt . '
                      </button>
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#saveModal">
                        Save
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- The Save Modal -->
              <div class="modal fade" id="saveModal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                      <h4 class="modal-title">Save Successful</h4>
                      <button type="button" class="close" data-dismiss="modal">
                        &times;
                      </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                      <p>Saved successfully!</p>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

  ';
}
