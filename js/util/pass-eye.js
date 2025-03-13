'use strict';

document.getElementById('passEye').addEventListener('click', function() {
  console.log('eye toggled');
  const passwordInput = document.getElementById('pass');
  const icon = this.children[0];

  // Toggle the input type
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});

// show/hide password in confirm pass field
document.getElementById('confirmPassEye').addEventListener('click', function() {
  console.log('eye toggled (confirm pass)');
  const passwordInput = document.getElementById('confirmPass');
  const icon = this.children[0];
  console.log(icon.classList);
  // Toggle the input type
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});
