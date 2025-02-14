'use strict';

// get from url params
const params = new URLSearchParams(location.search);
/** @var {int} */
const id = Number(params.get('id'));
/** @var {object} */
let barPerms = {};
/** @var {object} */
let genPerms = {};
/** @var {bool} */
let editMode = false;
/** @var {int} */
let permissionsID;

// old function for popover (not working for some reason)
// $(function() {
//   $('.form-group').popover({
//     container: 'body',
//   });
// });

// if id was provided, enter edit mode
if (id) {
  editMode = true;
  // Immediately Invoked Function Expression (IIFE)
  (async () => {
    // console.log('id founhd');
    // get existing data of the role
    $.ajax({
      type: 'POST',
      url: '../api/get_role_and_permissions.php',
      data: {
        id: id,
      },
      success: res => {
        /** @type {object} */
        res = JSON.parse(res);
        console.log('Res is: ' + JSON.stringify(res));

        // fill in role name
        $('#roleName').attr('value', res.role.name);

        // barangay allowed or not
        /** @type {bool} */
        const barAllowed = res.role.allow_barangay;

        // display barangay perms, if allowed
        if (barAllowed == 1) {
          $('#bar-perms-form').css('display', 'block');
        }

        // save permissions id for later
        permissionsID = res.role.permissions_id;

        // save and reset all to zero
        if (barAllowed == 1) {
          barPerms = structuredClone(res.barPerms); // this function creates a true deep copy of the object. Doesn't support old browsers
          Object.keys(barPerms).forEach(value => {
            barPerms[value] = 0;
          });
        }
        genPerms = structuredClone(res.barPerms); // this function creates a true deep copy of the object. Doesn't support old browsers
        Object.keys(barPerms).forEach(value => {
          barPerms[value] = 0;
        });

        // fill in permissions checkboxes
        if (barAllowed == 1) {
          for (const key in res.barPerms) {
            console.log('Key is' + res.barPerms[key] + '\n');
            if (res.barPerms[key] === 1) {
              $('#bar-' + key).prop('checked', true); // use prop instead of attr for attributes without a value
            }
          }
        }
        for (const key in res.genPerms) {
          console.log('Key is' + res.genPerms[key] + '\n');
          if (res.genPerms[key] === 1) {
            $('#' + key).prop('checked', true); // use prop instead of attr for attributes without a value
          }
        }
      },
      error: res => {
        console.log(res);
      },
    });
  })();
}

$('#allowBarangay').on('click', e => {
  // get the jquery element
  const checkbox = $(e.target);
  const barForm = $('#bar-perms-form');

  // if checked, hide the barangay permissions
  if (!checkbox.prop('checked')) {
    barForm.css('display', 'none');
    // also reset all checkboxes
    $('#bar-perms-form input[type=checkbox]').prop('checked', false);
  } else {
    barForm.css('display', 'block');
  }
});

//cancel button confirmation
$('#cancel-btn').on('click', async () => {
  const confirm = await showConfirmationDialog(
    'Are you sure? Your changes will not be saved.',
    'No',
    'Yes',
  );
  if (confirm) {
    location.href = 'roles.php';
  } else {
    console.log('cancelled');
  }
});

// when submitting the form
$('#save-role-btn').on('click', async () => {
  if (loading) return;
  toggleLoading();

  console.log('Edit mode: ' + editMode);

  // get all users to compare existing usernames & emails
  /** @type {array} */
  const roles = await fetch(`../api/get_roles.php`).then(async res => {
    if (!res.ok) {
      $('#alert').html(
        '<strong>Error!</strong> An unexpected error occurred.' +
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
          '</button>',
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
  $('#alert').empty();

  // get input values
  let formData = new FormData(document.querySelector('#crud-role-content'));
  let permissions = [];
  const roleName = formData.get('roleName').trim();
  const allowBarangay = formData.get('allowBarangay');
  // if edit mode, use keys and fill them. Needed for unchecking the previously checked items
  if (editMode) {
    permissions = allPermissions;
    for (let key of formData.keys()) {
      key = key.trim();
      if (key != 'roleName') {
        if (key != 'allowBarangay') {
          console.log('adding key ' + key);
          permissions[key] = 1;
        }
      }
    }
  } else {
    // use 'of' keyword because this is a iterator, not an array
    for (let key of formData.keys()) {
      console.log('Key was ' + key);
      key = key.trim();
      if (key != 'roleName') {
        if (key != 'allowBarangay') {
          console.log('adding key ' + key);
          permissions.push(key);
        }
      }
    }
  }

  // if edit mode, remove the item that matches the name of the current editing one.
  if (editMode) {
    // find index of the role
    let index = roles.findIndex(role => role.name == roleName); // use findIndex instead of indexOf because we are looking at the objects instead of arrays
    console.log(index);
    // if name exists
    if (index !== -1) {
      roles.splice(index, 1); // Removes the first match only
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
  // console.log(JSON.stringify(roles));
  if (roles.some(value => value.name == roleName)) {
    addError($('#roleName'), 'A role with that name already exists.');
    ok = false;
  }

  // PERMISSIONS CHECK
  if (
    !editMode &&
    (typeof permissions == 'object'
      ? Object.keys(permissions).length
      : permissions.length) == 0
  ) {
    console.log('Permissions is ' + JSON.stringify(permissions));
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
  formData = new FormData();
  formData.set('role_name', roleName);
  formData.set('permissions', JSON.stringify(permissions));
  formData.set('allow_barangay', allowBarangay ?? false);
  // id from search params
  if (editMode) {
    console.log(permissionsID);
    formData.append('id', id);
    formData.append('permissions_id', permissionsID);
  }
  for (const [key, value] of formData.entries()) {
    console.log(key + ' ' + value);
  }
  $.ajax({
    type: 'POST',
    url: '../api/' + (editMode ? 'edit_role.php' : 'create_role.php'),
    data: formData, // Use 'data' instead of 'body'
    processData: false, // Prevent jQuery from processing the FormData
    contentType: false, // Prevent jQuery from setting content type
    success: function(result) {
      // check if null, empty, false, 0, infinity, etc
      if (!result) {
        alert('Role ' + (editMode ? 'edited' : 'created') + ' successfully!');
        location.href = 'roles.php';
        // location.reload(); // reload because it needs to get latest data
        // $('#main-toast-container').append(
        //   addToast('Success!', 'User created successfully.'),
        // );
      } else {
        console.log('error!: ' + result);
      }
    },
  });
  toggleLoading();
});
