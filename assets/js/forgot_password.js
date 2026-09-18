$('#cancel').on('click', async () => {
  if (loading) return;
  loading = true;
  // console.log('backing up');
  const shouldCancel = await showConfirmationDialog(
    'Are you sure to cancel?\nYou will be redirected to login page.',
    'No',
    'Yes',
  );
  if (shouldCancel) location.href = 'login.php';
  loading = false;
});

let currentStep = 0;
const steps = document.querySelectorAll('.step');
const forms = ['email-form', 'code-form', 'password-form'];

async function showStep(index) {
  if (index >= 1) {
    $('#prevStep').removeClass('d-none');
  } else {
    $('#prevStep').addClass('d-none');
  }
  steps.forEach((step, i) => step.classList.toggle('active', i === index));
}

// populate error
function addErr(id, text) {
  $('#' + id).text(text);
}

// checks if code matches in server
function validateCode() {
  return true;
}

// checks if new password is valid
function validateNewPass() {
  return true;
  const newPass = $('#newPass')
    .val()
    ?.trim();
  if (newPass === '') {
    addErr('newPassErr', 'Cannot be empty.');
  }
  else if (newPass.length < 8) {
    addErr('newPassErr', 'Password must be at least 8 characters.');
  }
  return new Promise((res, rej) => {
    try {
      $.ajax({
        url: '..api/forgot_password.php',
        type: 'POST',
        data: {
          pass: newPass,
        },
        success: data => {
          console.log(data);
          res(true);
        },
        error: err => {
          console.error(err);
          res(false);
        },
      });
    } catch (error) {
      rej(error);
    }
  });
}

// checks if email exists in database
function emailExists() {
  return true;
}

// request code from server
function generateCode() {
  return true;
}

$('#nextStep').on('click', async e => {
  e.preventDefault();
  let shouldProceed = false;
  if (currentStep == 0 && emailExists()) shouldProceed = true;
  else if (currentStep == 1 && validateCode()) shouldProceed = true;
  else if (currentStep == 2 && validateNewPass()) shouldProceed = true;
  if (currentStep < steps.length - 1 && shouldProceed) {
    currentStep++;
    await showStep(currentStep);
  }
});

$('#prevStep').on('click', async () => {
  if (currentStep > 0) {
    currentStep--;
    await showStep(currentStep);
  }
});
