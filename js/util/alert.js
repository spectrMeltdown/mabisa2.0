"use strict";

function addAlert(id, msg, title = "Error: ") {
  $(`#${id}`).html(
    '<div id="' +
      `#${id}` +
      '" class="alert alert-danger alert-dismissible fade show" role="alert">' +
      `<strong>${title}</strong> ${msg}` +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      "</button>" +
      "</div>",
  );
}

function resetAlert(id) {
  $("." + id).removeClass("show");
}
