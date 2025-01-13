"use strict";

function addAlert(id, msg, title = "Error:") {
  const alertClass = $("." + id);
  alertClass.html(
    '<div class="alert" role="alert">' +
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
