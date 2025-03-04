'use strict';

$('#readNotifModal').on('show.bs.modal', async e => {
  // const id = $(e.relatedTarget).data('id');
  const title = $(e.relatedTarget).data('title');
  const message = $(e.relatedTarget).data('message');
  const id = $(e.relatedTarget).data('id');

  let isInFolder = $('#isInFolder').length != 0;
  $.ajax({
    url: (isInFolder ? '../../' : '../') + 'api/mark_as_read.php',
    data: {
      id: id,
    },
    success: data => {
      console.log(data);
    },
    error: err => {
      console.log(err.responseText);
    },
  });
  if (title) $('#notifModalLabel').text(title);
  if (message) $('#notifModalMessage').text(message);
});

$('#dismissBtn')
  .off('click')
  .on('click', () => {
    console.log('confirm clicked');
  });

$('#readNotifModal')
  .off('hidden.bs.modal')
  .on('hidden.bs.modal', () => {
    console.log('cancelled');
    window.location.href = document.referrer;
  });
