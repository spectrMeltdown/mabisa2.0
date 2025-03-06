'use strict';

$('#readNotifModal').on('show.bs.modal', async e => {
  // const id = $(e.relatedTarget).data('id');
  const title = $(e.relatedTarget).data('title');
  const message = $(e.relatedTarget).data('message');
  const id = $(e.relatedTarget).data('id');
  const fileLink = $(e.relatedTarget).data('file-link');

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
  if (message) {
    let link = '';
    if (fileLink) {
      link = `<a href="${fileLink}" class="card-link">Go to submission</a>`;
    }
    $('#notifModalMessage').html(
      `<p class="card-text">${message}</p><br>${link}`,
    );
  }
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
    location.reload();
  });
