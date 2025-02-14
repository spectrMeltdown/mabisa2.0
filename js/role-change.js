'use strict';

// get from url params
const params = new URLSearchParams(location.search);
/** @var {int} */
const id = Number(params.get('id'));
/** @var {bool} */
let editMode = false;
/** @var {int} */
let barPermID;
/** @var {int} */
let genPermID;

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
        const barAllowed = res.role.allow_bar;

        // display barangay perms, if allowed
        if (barAllowed == 1) {
          $('#bar-perms-form').css('display', 'block');
          showGenAssessItems(false);
        }

        // save permissions id for later
        barPermID = res.role.bar_perms;
        genPermID = res.role.gen_perms;

        // save and reset all to zero
        // /** @var {object} */
        // let barPerms = {};
        // /** @var {object} */
        // let genPerms = {};

        // if (barAllowed == 1) {
        //   barPerms = structuredClone(res.barPerms); // this function creates a true deep copy of the object. Doesn't support old browsers
        //   Object.keys(barPerms).forEach(value => {
        //     barPerms[value] = 0;
        //   });
        // }
        // genPerms = structuredClone(res.genPerms); // this function creates a true deep copy of the object. Doesn't support old browsers
        // Object.keys(genPerms).forEach(value => {
        //   genPerms[value] = 0;
        // });

        // fill in permissions checkboxes
        if (barAllowed == 1 && res.barPerms) {
          for (const key in res.barPerms) {
            console.log('Key is' + res.barPerms[key] + '\n');
            if (res.barPerms[key] === 1) {
              $('#bar-' + key).prop('checked', true); // use prop instead of attr for attributes without a value
            }
          }
        }
        if (res.genPerms) {
          for (const key in res.genPerms) {
            console.log('Key is' + res.genPerms[key] + '\n');
            if (res.genPerms[key] === 1) {
              $('#' + key).prop('checked', true); // use prop instead of attr for attributes without a value
            }
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

  // if checked, display the barangay permissions
  if (checkbox.prop('checked')) {
    barForm.css('display', 'block');
    showGenAssessItems(false);
  } else {
    barForm.css('display', 'none');
    showGenAssessItems(true);
    // reset all checkboxes
    $('#bar-perms-form input[type=checkbox]').prop('checked', false);
  }
});

function showGenAssessItems(newState) {
  const genAssessmentItems = $('#gen-perms-form').find(
    '[id*="assessment"].perm-container',
  );
  const checkboxes = genAssessmentItems.find('input[type="checkbox"]');
  if (newState) {
    // display the hidden items again
    genAssessmentItems.removeClass('d-none');
    genAssessmentItems.addClass('d-inline-block');
  } else {
    // hide global scope perms for the ones already defined in barangay perms
    genAssessmentItems.removeClass('d-inline-block');
    genAssessmentItems.addClass('d-none');
    // also reset their checkboxes
    checkboxes.prop('checked', false);
  }
}

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

  // get all role names for comparison
  /** @type {array} */
  const roles = await fetch(`../api/get_role_names.php`).then(async res => {
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

  // for checking all checkes for the form passed
  let ok = true;

  // start with a clean slate
  resetFieldStates();
  $('#alert').empty();

  // get input values
  const roleForm = new FormData(document.querySelector('#role-form'));
  const genPermsForm = new FormData(document.querySelector('#gen-perms-form'));
  const barPermsForm = new FormData(document.querySelector('#bar-perms-form'));
  const roleName = roleForm.get('roleName').trim();
  const allowBarangay = roleForm.get('allowBarangay');

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

  // GEN PERMISSIONS CHECK
  let emptyGenPerms = true;

  // loop the entries. If there was an entry, then bar perms is not empty
  for (const perm of genPermsForm.entries()) {
    console.log(perm);
    emptyGenPerms = false;
    break;
  }
  //  display error if not gen perms selected, and no barangay permissions selected as well
  // its okay to have no gen permissions when bar permissions are enabled
  if (emptyGenPerms && !allowBarangay) {
    console.log('gen perms error is ' + JSON.stringify(genPermsForm));
    $('#gen-permissions-alert').text('Please select at least one permission.');
    ok = false;
  }

  // BAR PERMISSIONS CHECK
  let emptyBarPerms = true;

  // loop the entries. If there was an entry, then bar perms is not empty
  for (const perm of barPermsForm.entries()) {
    console.log(perm);
    emptyBarPerms = false;
    break;
  }
  if (
    // check if allow barangay checkbox is checked
    allowBarangay &&
    // check if not bar permissions are selected
    emptyBarPerms
  ) {
    console.log('bar perms error is ');
    for (const e of barPermsForm.entries()) {
      console.log(e);
    }
    $('#bar-permissions-alert').text('Please select at least one permission.');
    ok = false;
  }

  // cancel if not okay
  if (!ok) {
    console.log('form not ok!');
    toggleLoading();
    return;
  }

  // construct filtered formdata for submitting
  // console.log('final form data');
  let barPermsArr = [];
  let genPermsArr = [];
  // convert the FormData to array
  for (const key of genPermsForm.keys()) {
    console.log('gen ' + key);
    genPermsArr.push(key);
  }
  for (const key of barPermsForm.keys()) {
    console.log('bar ' + key);
    barPermsArr.push(key);
  }

  // construct the final data to send
  const finalData = new FormData();
  finalData.set('role_name', roleName);
  finalData.set('barPerms', JSON.stringify(barPermsArr));
  finalData.set('genPerms', JSON.stringify(genPermsArr));
  finalData.set('allow_bar', allowBarangay ?? false);
  // id from search params
  if (editMode) {
    console.log(barPermID);
    console.log(genPermID);
    finalData.append('id', id);
    finalData.append('barPermID', barPermID);
    finalData.append('genPermID', genPermID);
  }
  for (const [key, value] of finalData.entries()) {
    console.log(key + ' + ' + value);
  }
  $.ajax({
    type: 'POST',
    url: '../api/' + (editMode ? 'edit_role.php' : 'create_role.php'),
    data: finalData, // Use 'data' instead of 'body'
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
    error: e => {
      console.log(e.responseText);
    },
  });
  toggleLoading();
});

// adding error message function
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

// reset function for error states
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
  $('#gen-permissions-alert').text('');
  $('#bar-permissions-alert').text('');
}
