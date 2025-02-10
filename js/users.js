'use strict';

if ($('#user_dataTable').find('p').length === 0) {
  // Initialize DataTable if no <p> element is found
  $('#user_dataTable').DataTable({
    language: {
      emptyTable: 'No matching records found.',
    },
    columns: [
      { title: 'Fullname' },
      { title: 'Username' },
      { title: 'Role' },
      { title: 'Barangay' },
      { title: 'Actions' },
    ],
  });
}

// handle user delete btn
$('.delete-user-btn').on('click', async (e) => {
  if (loading) return;
  toggleLoading();

  const shouldDelete = await showConfirmationDialog(
    'Are you sure you want to delete this user?',
    'No',
    'Yes',
  );
  if (shouldDelete) {
    // use currenttarget instead of target, ot use closest()
    const userID = $(e.currentTarget).data('id');
    console.log('ID to remove:' + userID);
    $.ajax({
      type: 'POST',
      url: '../api/delete_user.php',
      data: {
        id: userID,
      },
      success: function (result) {
        if (!result) {
          $('#crud-user').modal('hide');
          location.reload();
          $('#main-toast-container').append(
            addToast('Success!', 'User created successfully.'),
          );
        } else {
          console.log('error!: ' + result);
        }
      },
    });
  }
  toggleLoading();
});
