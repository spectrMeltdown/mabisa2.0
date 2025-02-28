'use strict';

$('#readNotifModal').on('show.bs.modal', async e => {
  // const id = $(e.relatedTarget).data('id');
  const title = $(e.relatedTarget).data('title');
  const message = $(e.relatedTarget).data('message');

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
  });
