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
  if (currentStep == 0) {
    $('#prevStep').removeClass('d-none');
  } else {
    $('#prevStep').addClass('d-none');
  }
  steps.forEach((step, i) => step.classList.toggle('active', i === index));
}

// checks if code matches in server
function validateCode() {

}

// checks if new password is valid
function validateNewPass() {

}

// checks if email exists in database
function emailExists() {

}

// request code from server
function generateCode() {

}

$('#nextStep').on('click', async () => {
  let shouldProceed = false;
  if (currentStep == 0 && emailExists()) shouldProceed = true;
  if (currentStep == 1 && validateCode()) shouldProceed = true;
  if (currentStep == 2 && validateNewPass()) shouldProceed = true;
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
