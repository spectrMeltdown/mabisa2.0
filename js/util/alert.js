'use strict';

function addAlert(msg, title = 'Error: ', id = 'alert') {
  $(`#${id}`).append(
    `<strong>${title}</strong> ${msg}` +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>',
  );
  $(`.${id}`).append(
    `<strong>${title}</strong> ${msg}` +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>',
  );
}

function resetAlert(id) {
  $('.' + id).removeClass('show');
  $('#' + id).removeClass('show');
  $('#' + id).html();
}
