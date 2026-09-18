'use strict';

$('.passEye').on('click', e => {
  console.log('eye toggled');
  const passwordInputs = document.getElementsByClassName('pass');
  const icon = $(e.target);
  // console.log(passwordInputs);
  // console.log(icon);

  // Toggle the input type
  for (const passwordInput of passwordInputs) {
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.addClass('fa-eye').removeClass('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.addClass('fa-eye-slash').removeClass('fa-eye');
    }
  }
});
