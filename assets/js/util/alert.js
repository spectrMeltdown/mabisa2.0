'use strict';

function addAlert(msg, title = 'Error: ', id = 'alert') {
  let element = $(`#${id}`);
  if (!element.hasClass('show')) element.addClass('show');
  element.append(
    `<strong>${title}</strong> ${msg}` +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>',
  );
}

function resetAlert(id) {
  // $('.' + id).removeClass('show');
  $('#' + id).removeClass('show');
  $('#' + id).empty();
}
