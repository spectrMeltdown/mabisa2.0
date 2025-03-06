<div class="modal fade" id="allNotifModal" tabindex="-1" aria-labelledby="allNotifModalLabel" aria-hidden="true">
  <div class="modal-dialog modal modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="allNotifModalLabel">Notifications</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-white">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="notificationList" style="max-height: 400px; overflow-y: auto;">
        Loading...
      </div>
      <div class="modal-footer">
        <div id="loading" style="display: none;">Loading...</div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="allNotifDismissBtn">Close</button>
      </div>
    </div>
  </div>
</div>