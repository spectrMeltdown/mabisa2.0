async function showConfirmationDialog(message) {
  if (message) {
    $('#confirmationMessage').text(message);
  }
  
  const confirmationModal = new bootstrap.Modal($('#confirmationModal'));
  confirmationModal.show();

  return new Promise((resolve)=> {
    $('#confirmBtn').off('click').on('click', ()=> {
      resolve(true);
      confirmationModal.hide();
    });
    
    $('#confirmationModal').off('hidden.bs.modal').on('hidden.bs.modal', ()=> {
      resolve(false);
    });
  });
}