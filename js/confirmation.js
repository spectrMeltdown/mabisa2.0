async function showConfirmationDialog(message) {
  if (message) {
    $("#confirmMessage").text(message);
  }

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
