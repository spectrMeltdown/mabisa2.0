'use strict';

const defaultAlert = '<div class="alert"></div>';
let editMode = undefined;
// $('#crud-user').on('show.bs.modal', function(event) {
//   if (loading) return;
//   toggleLoading();

//   if ($(event.relatedTarget).hasClass('edit-user-btn')) {

//   } else if ($(event.relatedTarget).hasClass('add-user-btn')) {
//   }
//   toggleLoading();
// });

// reset the form element in crud user dialog
// $('#crud-user').on('hidden.bs.modal', () => {
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

// for adding user
// listen when the admin changes selection, and display additional inputs
// if (document.getElementById('role')) {
//   document.getElementById('role').addEventListener('change', event => {
//     console.log('Changed role');
//     if (loading) return;
//     toggleLoading();
//     toggleLoading();
//   });
// }

// when submitting the form
$('#save-role-btn').on('click', async () => {
  if (loading) return;
  toggleLoading();

  // get all users to compare existing usernames & emails
  /** @type {array} */
  const roles = await fetch(`../api/get_roles.php`).then(async res => {
    if (!res.ok) {
      $('#alert').html(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          '<strong>Error!</strong> An unexpected error occurred.' +
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
          '</button>' +
          '</div>',
      );
      throw new Error('Network problem!');
    }
    const text = await res.text();
    console.log(text);
    return JSON.parse(text);
  });
  let ok = true;

  // define reset function
  function resetFieldStates() {
    $('#roleName').removeClass('is-invalid');
    $('#roleName')
      .find('.invalid-feedback')
      .first()
      .text('');
    // $('#permissions').removeClass('is-invalid');
    // $('#permissions')
    //   .find('.invalid-feedback')
    //   .first()
    //   .text('');
    $('#permissions-alert').text('');
  }

  // define adding error message function
  function addError(element, message, customFeedbackElement) {
    if (customFeedbackElement) {
      element.find('.invalid-feedback').text(message);
    } else {
      element
        .next('.invalid-feedback')
        .first()
        .text(message);
    }
    element.addClass('is-invalid');
  }

  // start with a clean slate
  resetFieldStates();
  $('#alert').html(defaultAlert);

  // get input values
  let formData = new FormData(document.querySelector('#crud-role-content'));
  const roleName = formData.get('roleName').trim();
  const permissions = [];
  const allowBarangay = formData.get('allowBarangay');
  // use 'of' keyword because this is a iterator, not an array
  for (let key of formData.keys()) {
    key = key.trim();
    if (key != 'roleName' && key != 'allowBarangay') {
      console.log('adding key ' + key);
      permissions.push(key);
    }
  }

  // ROLE NAME CHECK
  if (roleName === '' || roleName === null) {
    addError($('#roleName'), 'Please provide a name.');
    ok = false;
  }
  if (roleName.length > 100) {
    addError($('#roleName'), 'Please choose a shorter name.');
    ok = false;
  }
  console.log(JSON.stringify(roles));
  if (!editMode && roles.some(value => value.name == roleName)) {
    addError($('#roleName'), 'A role with that name already exists.');
    ok = false;
  }

  // PERMISSIONS CHECK
  if (permissions.length === 0) {
    $('#permissions-alert').text('Please select at least one permission.');
    ok = false;
  }
  // cancel if not okay
  if (!ok) {
    console.log('form not ok!');
    toggleLoading();
    return;
  }
  // construct filtered formdata for submitting
  console.log('permissions is ' + permissions);
  formData = new FormData();
  formData.set('role_name', roleName);
  formData.set('permissions', JSON.stringify(permissions));
  formData.set('allow_barangay', allowBarangay ?? false);
  if (!editMode) {
    console.log('not edit mode');
    $.ajax({
      type: 'POST',
      url: '../api/create_role.php',
      data: formData, // Use 'data' instead of 'body'
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false, // Prevent jQuery from setting content type
      success: function(result) {
        // check if null, empty, false, 0, infinity, etc
        if (!result) {
          alert('Role created successfully!');
          history.back(); // go back
          location.reload(); // reload because it needs to get latest data
          // $('#main-toast-container').append(
          //   addToast('Success!', 'User created successfully.'),
          // );
        } else {
          console.log('error!: ' + result);
        }
      },
    });
  } else {
    // get from url params
    const params = new URLSearchParams(location.search);
    formData.append('id', params.get('id'));
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
          // $('#main-toast-container').append(
          //   addToast('Success!', 'User successfully edited.'),
          // );
        } else {
          /** @type {array | null} */
          const errorInfo = result['errorInfo'];
          if (
            errorInfo.some((/** @type {string} */ item) =>
              item.includes('Duplicate'),
            )
          ) {
            $('#alert').text('The role name already exists.');
          }
          console.log('error!: ' + result);
        }
      },
    });
  }
  toggleLoading();
});
