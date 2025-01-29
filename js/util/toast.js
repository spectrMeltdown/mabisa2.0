"use strict";
// adds a toast to the toast container
function addToast(title, body, small = "", imgSrc = "") {
  let id = self.crypto.randomUUID();
  return `
  <div id='${id}' class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <img src="${imgSrc}" class="rounded me-2" alt="...">
      <strong class="me-auto">${title}</strong>
      <small class="text-muted">${small}</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">${body}</div>
  </div>`;
}
