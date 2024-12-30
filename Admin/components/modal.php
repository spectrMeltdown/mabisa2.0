<?php

function createModal(string $btnTxt, string $title, string $content, string $formAttrs = '', string $script = '', string $cancelBtnTxt = 'Close', string $saveBtnTxt = 'Save'): string
{
  /** @var string */
  $topWrapTag = empty($formAttrs) ? '' : '<form ' . $formAttrs . ' id="modalForm">';
  $bottomWrapTag = empty($formAttrs) ? '' : '</form>';
  return '
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
                    ' . $topWrapTag . '
                    <div class="modal-body">
                      ' . $content . '
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        ' . $cancelBtnTxt . '
                      </button>
                      <button ' . (empty($formAttrs) ? 'type="button"' : 'type="submit"') . ' class="btn btn-primary" ' . (empty($formAttrs) ? 'data-dismiss="modal"' : '') . '>
                        ' . $saveBtnTxt . '
                      </button>
                    </div>
                    ' . $bottomWrapTag . '
                  </div>
                </div>
              </div>

              ' . $script . '
            <script>
              document.getElementById("myModal").addEventListener("hidden.bs.modal", function () {
                document.getElementById("modalForm").reset();
              });
            </script>
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
          </div>

  ';
}