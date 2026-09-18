'use strict';

$('#loginBtn').on('click', e => {
  // console.log("login btn pressed");
  if (loading) return;
  loading = true;

  let ok = true;
  const username = $('#username')
    .val()
    .trim();
  const password = $('#password')
    .val()
    .trim();
  const rememberMe = $('#rememberMe').prop('checked') ?? false;

  resetAlert('alert');
  if (!password) {
    addAlert('Password cannot be empty.');
    ok = false;
  }
  if (!username) {
    addAlert('Username cannot be empty.');
    ok = false;
  }
  if (!ok) {
    loading = false;
    return;
  }
  $.ajax({
    url:'../api/login.php',
    method: 'POST' ,
    data: {
      username: username,
      password: password,
      rememberMe: rememberMe,
    },
    success: user => {
      console.log('user is: ' + JSON.stringify(user));

      // show error on #alert if invalid
      if (user === undefined ? true : user.error) {
        addAlert(
          user === undefined ? 'Sorry, an unknown error occurred.' : user.error,
        );
        loading = false;
        return;
      }
      // redirect to dashboard
      location.href = 'dashboard.php';
    },
    error: err => {
      console.error(err.responseText);
    }
  });
  loading = false;
});

// show/hide in password field
document.getElementById('passEye').addEventListener('click', function() {
  console.log('eye toggled');
  const passwordInput = document.getElementById('password');
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
