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
  columns: [{ data: 'Role' }, { data: 'Permissions' }, { data: 'Actions' }],
});

// $('#crud-role').on('show.bs.modal', function(event) {
//   if (loading) return;
//   toggleLoading();

//   if ($(event.relatedTarget).hasClass('edit-user-btn')) {
//     // console.log("editing");
//     editMode = true;

//     // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
//     origModalLabel = modalLabel.textContent;
//     modalLabel.textContent = 'Edit User'; // Update the modal label text

//     // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
//     origPassLabel = passLabel.textContent;
//     passLabel.textContent = 'New Password'; // Update the modal label text

//     // hide confirm password because we are editing
//     $('#confirmPassField').css('display', 'none');
//     currentUserID = $(event.relatedTarget).data('id'); // Get the user ID from the clicked button

//     // GET request to ../api/get_user.php
//     fetch(`../api/get_user.php?id=${currentUserID}`)
//       .then(response => response.json())
//       .then(user => {
//         // Assuming 'data' contains the user info for filling the modal
//         // console.log("Data is: " + JSON.stringify(user));
//         // Fill in the form inputs with the response data
//         document.getElementById('fullName').value = user.fullName || '';
//         document.getElementById('username').value = user.username || '';
//         document.getElementById('email').value = user.email || '';
//         document.getElementById('mobileNum').value =
//           `+63${user.mobileNum}` || '';

//         // Handle the 'role' select element
//         const roleSelect = document.getElementById('role');
//         const roleOptions = roleSelect.options;
//         for (let i = 0; i < roleOptions.length; i++) {
//           if (roleOptions[i].text === 'Select one') {
//             roleSelect.remove(i); // Remove the 'Select one' option
//             break;
//           }
//         }
//         // Add selected attribute to the matching role
//         const roleOption = Array.from(roleSelect.options).find(
//           option => option.text === user.role,
//         );
//         if (roleOption) {
//           roleOption.selected = true;
//           roleSelect.value = roleOption.value;
//         }

//         // Handle the 'barangay' select element
//         const barangaySelect = document.getElementById('barangay');
//         const barangayDiv = document.getElementById('barangayDiv');
//         console.log(`barangay was: ${user.barangay}`);
//         if (user.barangay && user.barangay !== 'N/A') {
//           barangayDiv.style.display = 'inline-block';
//           const barangayOption = Array.from(barangaySelect.options).find(
//             option => option.text === user.barangay,
//           );
//           if (barangayOption) {
//             barangaySelect.remove(barangaySelect.selectedIndex); // Remove the 'Select one' option
//             barangayOption.selected = true;
//           }
//         } else {
//           // If barangay is null or 'N/A', remove 'Select one' option
//           const barangaySelectOption = Array.from(barangaySelect.options).find(
//             option => option.text === 'Select one',
//           );
//           if (barangaySelectOption) {
//             barangaySelect.remove(barangaySelectOption.index);
//           }
//         }

//         // Hide the loading spinner and show the modal content
//         document.getElementById('loadingSpinner').classList.remove('d-flex');
//         document.getElementById('loadingSpinner').classList.add('d-none');
//         document
//           .getElementById('crud-user-modal-content')
//           .classList.remove('d-none');
//       })
//       .catch(error => {
//         console.error('Error fetching data:', error);
//         // Handle any errors (e.g., show an error message)
//         document.getElementById('loadingSpinner').classList.remove('d-flex');
//         document.getElementById('loadingSpinner').classList.add('d-none');
//         document
//           .getElementById('crud-user-modal-content')
//           .classList.remove('d-none');
//       });
//   } else if ($(event.relatedTarget).hasClass('add-user-btn')) {
//     // console.log("adding");
//     editMode = false;
//     // Hide the loading spinner and show the modal content
//     document.getElementById('loadingSpinner').classList.remove('d-flex');
//     document.getElementById('loadingSpinner').classList.add('d-none');
//     document
//       .getElementById('crud-user-modal-content')
//       .classList.remove('d-none');
//     // Show the edit modal crud-user
//   }
//   toggleLoading();
// });

// // reset the form element in crud user dialog
// $('#crud-role').on('hidden.bs.modal', () => {
//   console.log('modal hidden');
//   const form = this.querySelector('form');
//   if (form) form.reset();

//   // revert the text to original
//   modalLabel.textContent = origModalLabel;
//   passLabel.textContent = origPassLabel;

//   // hide barangay option
//   const barangayDiv = document.getElementById('barangayDiv');
//   barangayDiv.style.display = 'none';

//   // display confirm pass again
//   $('#confirmPassField').css('display', 'inline-block');

//   // remove the userID
//   currentUserID = undefined;

//   // hide the additional fields per role
//   toggleAuditor(false);
//   toggleSecretary(false);
//   auditorBarangays = [];

//   //hide alerts
//   $('#alert').html(defaultAlert);
// });

// when submitting the form
$('#save-role-btn').on('click', async () => {
  if (loading) return;
  toggleLoading();

  // get all users to compare existing usernames & emails
  const users = await fetch(`../api/get_users.php`).then(response => {
    if (!response.ok) {
      $('#alert').html(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          '<strong>Error!</strong> Network error!.' +
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
          '</button>' +
          '</div>',
      );
      throw new Error('Network problem!');
    }
    return response.json();
  });
  let ok = true;

  // console.log($("#username").val());
  // get input values
  const username = $('#username')
    .val()
    ?.trim();
  const fullName = $('#fullName')
    .val()
    ?.trim();
  const email = $('#email')
    .val()
    ?.trim();
  const mobileNum = $('#mobileNum')
    .val()
    ?.trim();
  const role = $('#role')
    .val()
    ?.trim();
  const barangay = $('#barangay')
    .val()
    ?.trim();
  const confirmPass = $('#confirmPass')
    .val()
    ?.trim();
  const password = $('#pass')
    .val()
    ?.trim();

  function resetFieldStates() {
    $('#username').removeClass('is-invalid');
    $('#username')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#fullName').removeClass('is-invalid');
    $('#fullName')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#email').removeClass('is-invalid');
    $('#email')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#mobileNum').removeClass('is-invalid');
    $('#mobileNum')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#role').removeClass('is-invalid');
    $('#role')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#barangay').removeClass('is-invalid');
    $('#barangay')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#confirmPass').removeClass('is-invalid');
    $('#confirmPass')
      .find('.invalid-feedback')
      .first()
      .text('');
    $('#pass').removeClass('is-invalid');
    $('#pass')
      .find('.invalid-feedback')
      .first()
      .text('');
  }

  function addError(element, message, customFeedbackElement) {
    if (customFeedbackElement) {
      element
        .find('.invalid-feedback')
        .first()
        .text(message);
    } else {
      element
        .next('.invalid-feedback')
        .first()
        .text(message);
    }
    element.addClass('is-invalid');
  }

  resetFieldStates();

  // PASSWORD
  if (!editMode && (password == '' || password == null)) {
    addError($('#pass'), 'Password cannot be empty.', $('#passField'));
    ok = false;
  }
  if (!editMode && password.length < 8) {
    addError($('#pass'), 'Password must be at least 8 characters.');
    ok = false;
  }
  if (!editMode && password.length > 100) {
    addError($('#pass'), 'Password too long.');
    ok = false;
  }

  // CONFIRM PASS
  if (!editMode && (confirmPass == '' || confirmPass == null)) {
    addError(
      $('#confirmPass'),
      'Please type in the password again.',
      $('#confirmPassField'),
    );
    ok = false;
  }
  if (!editMode && password !== confirmPass) {
    addError($('#confirmPass'), 'Passwords do not match.');
    ok = false;
  }

  // USERNAME
  if (username == '' || username == null) {
    addError($('#username'), 'Username cannot be empty.');
    ok = false;
  }
  if (username.length > 100) {
    addError($('#pass'), 'Username too long.');
    ok = false;
  }
  if (
    !editMode &&
    users.some(user => user.username === username && user.id != currentUserID)
  ) {
    addError($('#username'), 'Username already taken.');
    ok = false;
  }

  // FULLNAME
  if (fullName == '' || fullName == null) {
    addError($('#fullName'), 'Name cannot be empty.');
    ok = false;
  }
  if (fullName.length > 100) {
    addError($('#fullName'), 'Name too long.');
    ok = false;
  }

  // EMAIL
  if (!validEmail(email)) {
    addError($('#email'), 'Invalid email.');
    ok = false;
  }
  if (
    !editMode &&
    users.some(user => user.email === email && user.id != currentUserID)
  ) {
    addError($('#email'), 'Email already taken.');
    ok = false;
  }
  if (email.length > 100) {
    addError($('#email'), 'Email too long.');
    ok = false;
  }
  if (email == '' || email == null) {
    addError($('#email'), 'Email cannot be empty.');
    ok = false;
  }

  // MOBILE NUMBER
  if (!validMobileNum(mobileNum)) {
    addError($('#mobileNum'), 'Invalid mobile number.');
    ok = false;
  }
  if (mobileNum == '+63' || mobileNum == '' || mobileNum == null) {
    addError($('#mobileNum'), 'Mobile number cannot be empty.');
    ok = false;
  }

  // BARANGAY
  if ((barangay == '' || barangay == null) && role === 'Secretary') {
    addError($('#barangay'), 'Please select a barangay.');
    ok = false;
  }

  // ROLE
  if (role == null || role == 'Select one') {
    addError($('#role'), 'Please select a role.');
    ok = false;
  }

  // FOR AUDITOR
  if (role === 'Auditor' && auditorBarangays.length == 0) {
    addError($('#role'), 'Please assign barangays to this auditor.');
    ok = false;
  }

  if (!ok) {
    console.log('form not ok!');
    toggleLoading();
    return;
  }

  $('#alert').html(defaultAlert);
  const formData = new FormData($('#crud-user-modal-content').get(0));
  // some extra work before submitting
  switch (role) {
    case 'Admin':
      formData.delete('barangay');
      formData.delete('auditorBarangays');
      break;
    case 'Secretary':
      formData.delete('auditorBarangays');
      break;
    case 'Auditor':
      formData.delete('barangay');
      formData.append('auditorBarangays', JSON.stringify(auditorBarangays));
      break;
    default:
      console.error('Unknown role when saving the user!');
  }
  if (!editMode) {
    console.log('not edit mode');
    $.ajax({
      type: 'POST',
      url: '../api/create_user.php',
      data: formData, // Use 'data' instead of 'body'
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false, // Prevent jQuery from setting content type
      success: function(result) {
        // check if null, empty, false, 0, infinity, etc
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
  } else {
    formData.append('id', currentUserID);
    $.ajax({
      type: 'POST',
      url: '../api/edit_user.php',
      data: formData, // Use 'data' instead of 'body'
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false, // Prevent jQuery from setting content type
      success: function(result) {
        // check if null, empty, false, 0, infinity, etc
        if (!result) {
          $('#crud-user').modal('hide');
          location.reload();
          $('#main-toast-container').append(
            addToast('Success!', 'User successfully edited.'),
          );
        } else {
          console.log('error!: ' + result);
        }
      },
    });
  }
  toggleLoading();
});

$('.delete-role-btn').on('click', async e => {
  if (loading) return;
  toggleLoading();

  const shouldDelete = await showConfirmationDialog(
    'Are you sure you want to delete this role?',
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
      success: function(result) {
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
