'use strict';

// account details enum
// const accountDataCamel = Object.freeze({
//   username: Symbol('username'),
//   email: Symbol('email'),
//   fullName: Symbol('fullName'),
//   mobileNum: Symbol('mobileNum'),
//   password: Symbol('password'),
// });

// const accountDataSnake = Object.freeze({
//   username: Symbol('username'),
//   email: Symbol('email'),
//   fullName: Symbol('full_name'),
//   mobileNum: Symbol('mobile_num'),
//   password: Symbol('password'),
// });

let origMobileNum;
let origFullName;
let origUsername;
let origEmail;

// get user data onload
document.addEventListener('DOMContentLoaded', async () => {
  const user = await fetch('../api/get_user.php?id=self')
    .then(res => res.json())
    .catch(e => {
      console.log('Error!' + e);
      throw e;
    });

  // assign user data to display fields
  origUsername = user['username'];
  origEmail = user['email'];
  origMobileNum = user['mobile_num'];
  origFullName = user['full_name'];
  $('#username').text(origUsername);
  $('#email').text(origEmail);
  $('#fullName').text(origFullName);
  $('#mobileNum').text('+63' + origMobileNum);

  const profilePicExists = await fileExists(user['profile_pic']);
  console.log('Profile pic exists:' + profilePicExists);
  $('#profilePic').attr(
    'src',
    profilePicExists ? user['profile_pic'] : '../img/default_profile.svg',
  );

  // register delete button event handler
  $('#deleteAccountBtn').on('click', async e => {
    const shouldDelete = await showConfirmationDialog();
    if (shouldDelete) {
      const userID = $(e.target).data('id');
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
  });
});

// clicking the change profile pic button
document.getElementById('changePicBtn').addEventListener('click', () => {
  if (loading) return;
  loading = true;

  const fileInput = document.getElementById('fileInput');
  fileInput.value = ''; // Clear previous selection
  fileInput.click(); // Trigger the file selector
  loading = false;
});

// hidden file input element
document.getElementById('fileInput').addEventListener('change', async event => {
  const file = event.target.files[0];

  if (!file) {
    return; // No file selected
  }

  // Check file size (<= 2MB)
  if (file.size > 2 * 1024 * 1024) {
    document.getElementById('alertDiv').innerText =
      'File size must not exceed 2MB.';
    document.getElementById('alertDiv').style.display = 'block';
    return;
  }

  const formData = new FormData();
  formData.append('profileImage', file);

  try {
    const response = await fetch('../api/profile_pic.php', {
      method: 'POST',
      body: formData,
    });

    if (!response.ok) {
      const error = await response.text();
      document.getElementById('alertDiv').innerText = error;
      document.getElementById('alertDiv').style.display = 'block';
      return;
    }

    // If successful, clear alert div
    document.getElementById('alertDiv').style.display = 'none';
    alert('Profile picture updated successfully!');
    location.reload();
  } catch (error) {
    document.getElementById('alertDiv').innerText =
      'An error occurred while uploading. Please try again.';
    document.getElementById('alertDiv').style.display = 'block';
  }
});

// individual edit buttons
$('#mobileNum-edit').on('click', async () => {
  /** @type {FormData} */
  const value = await changeProfileSettingDialog(
    'mobileNum',
    'Change Mobile Number',
    'Enter new mobile number:',
    origMobileNum,
  );
  if (value) updateAccData(value);
});
$('#fullName-edit').on('click', async () => {
  /** @type {FormData} */
  const value = await changeProfileSettingDialog(
    'fullName',
    'Change Full Name',
    'Enter new full name:',
    origFullName,
  );
  if (value) updateAccData(value);
});
$('#username-edit').on('click', async () => {
  /** @type {FormData} */
  const value = await changeProfileSettingDialog(
    'username',
    'Change Username',
    'Enter new username:',
    origUsername,
  );
  if (value) updateAccData(value);
});
$('#password-edit').on('click', async () => {
  /** @type {FormData} */
  const value = await changeProfileSettingDialog(
    'password',
    'Change Password',
    'Enter new password:',
  );
  if (value) updateAccData(value);
});
$('#email-edit').on('click', async () => {
  const value = await changeProfileSettingDialog(
    /** @type {FormData} */
    'email',
    'Change Email',
    'Enter new email:',
    origEmail,
  );
  if (value) updateAccData(value);
});

// create a enum (basically immutable object because js doesn't have enums)
const settingValues = [
  'mobileNum',
  'fullName',
  'password',
  'email',
  'username',
];
const setting = {};
for (let val in settingValues) {
  setting.val = val;
}
Object.freeze(setting);

function checkSetting(valToCheck) {
  for (let val in setting) {
    console.log('Val is ' + val);
    console.log('Val to check is ' + valToCheck);
    if (val == valToCheck) {
      return true;
    }
  }
  return false;
}

// show dialog to get new value, then return that value
async function changeProfileSettingDialog(setting, title, subtitle, oldValue) {
  if (checkSetting(setting)) {
    throw new Error('Invalid setting!');
  }
  $('#changeProfileSettingTitle').text(title);
  $('#changeProfileSettingSubtitle').text(subtitle);
  $('#newValueInput').attr('name', setting);

  switch (setting) {
    case 'mobileNum':
      $('#newValueInput').attr('type', 'tel');
      $('#newValueInput').attr('maxLength', '10');
      $('#newValueInput').attr('pattern', '^+?[0-9]*$');
      $('#newValueInput').attr('inputmode', 'numeric');
      $('#newValueInput').attr('autocomplete', 'tel');
      addPhonePrepend('newValueInput');
      break;
    case 'fullName':
      removePhonePrepend('newValueInput');
      $('#newValueInput').attr('type', 'text');
      $('#newValueInput').attr('maxLength', '100');
      $('#newValueInput').removeAttr('pattern');
      $('#newValueInput').removeAttr('inputmode');
      $('#newValueInput').attr('autocomplete', 'name');
      break;
    case 'password':
      removePhonePrepend('newValueInput');
      $('#newValueInput').attr('type', 'password');
      $('#newValueInput').attr('maxLength', '100');
      $('#newValueInput').removeAttr('pattern');
      $('#newValueInput').removeAttr('inputmode');
      $('#newValueInput').attr('autocomplete', 'new-password');
      break;
    case 'email':
      removePhonePrepend('newValueInput');
      $('#newValueInput').attr('type', 'email');
      $('#newValueInput').attr('maxLength', '100');
      $('#newValueInput').removeAttr('pattern');
      $('#newValueInput').removeAttr('inputmode');
      $('#newValueInput').attr('autocomplete', 'email');
      break;
    case 'username':
      removePhonePrepend('newValueInput');
      $('#newValueInput').attr('type', 'text');
      $('#newValueInput').attr('maxLength', '100');
      $('#newValueInput').removeAttr('pattern');
      $('#newValueInput').removeAttr('inputmode');
      $('#newValueInput').attr('autocomplete', 'username');
      break;
  }

  // setting the value last, because mobileNum added a prepend of +63
  console.log('old value was: ' + oldValue);
  $('#newValueInput').val(oldValue);

  const dialog = new bootstrap.Modal($('#changeProfileSettingDialog').get(0));
  dialog.show();
  return new Promise(resolve => {
    // return value if button clicked
    $('#changeProfileSettingSubmit')
      .off('click')
      .on('click', () => {
        const formData = new FormData($('#settingFormData').get(0));
        console.log('FormData was: ' + JSON.stringify(formData));
        resolve(formData);
        dialog.hide();
      });
    // end if closed
    $('#changeProfileSettingDialog').on('hidden.bs.modal', () => {
      resolve(null);
    });
  });
}

// function to apply changes to database
function updateAccData(/** @type {FormData} */ formData) {
  formData.append('id', 'self');
  try {
    $.ajax({
      url: '../api/edit_user.php',
      type: 'POST',
      data: formData,
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false,
      success: data => {
        console.log(data);
        // $('#crud-user').modal('hide');
        location.reload();
      },
      error: err => {
        console.error(err);
      },
    });
  } catch (error) {
    console.log(error);
  }
}
