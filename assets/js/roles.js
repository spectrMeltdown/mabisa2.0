'use strict';

// Initialize DataTable if no <p> element is found
$('#roles_dataTable').DataTable({
  language: {
    emptyTable: 'No matching records found.',
  },
  // columnDefs: [
  //   {
  //     targets: [1], // index of the permissions column
  //     searchable: false, // Disable search on
  //   },
  // ],
  columns: [
    { title: 'Role' },
    { title: 'Global Permissions' },
    { title: 'Barangay Permissions' },
    { title: 'Actions' },
  ],
});

$('.delete-role-btn').on('click', async e => {
  const confirmation = await showConfirmationDialog(
    'Are you sure you want to delete this role?',
    'No',
    'Yes',
  );
  if (!confirmation) return;
  const id = $(e.target).data('id');
  const genPerms = $(e.target).data('gen-perms-id');
  const barPerms = $(e.target).data('bar-perms-id');
  $.ajax({
    url: '../api/delete_role.php',
    type: 'POST',
    data: {
      id: id,
      genPerms: genPerms,
      barPerms: barPerms,
    },
    success: result => {
      console.log('Success: ' + result);
      location.reload();
    },
    error: e => {
      console.error(e.responseText);
    },
  });
});
