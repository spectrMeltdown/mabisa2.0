"use strict";

// show a confirmation dialog, returns bool
async function showConfirmationDialog(
  message,
  cancelText = "Cancel",
  confirmText = "Confirm",
) {
  if (message) $("#confirmMessage").text(message);
  if (cancelText) $("#cancelBtn").text(cancelText);
  if (confirmText) $("#confirmBtn").text(confirmText);

  const confirmationModal = new bootstrap.Modal(
    document.getElementById("confirmModal"),
  );
  confirmationModal.show();

  return new Promise((resolve) => {
    $("#confirmBtn")
      .off("click")
      .on("click", () => {
        console.log("confirm clicked");
        resolve(true);
        confirmationModal.hide();
      });

    $("#confirmModal")
      .off("hidden.bs.modal")
      .on("hidden.bs.modal", () => {
        console.log("cancelled");
        resolve(false);
      });
  });
}
