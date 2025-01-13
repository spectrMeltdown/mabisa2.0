"use strict";
// for debouncing
let loading = false;

$("#loginBtn").on("click", (e) => {
  console.log("login btn pressed");
  if (loading) return;
  loading = true;

  let ok = true;
  const uname = $("#username").val().trim();
  const pass = $("#password").val().trim();
  const rememberMe = $("#rememberMe").prop("checked");

  if (!pass) {
    addAlert("alert", "Password cannot be empty.");
    ok = false;
  }
  if (!uname) {
    addAlert("alert", "Username cannot be empty.");
    ok = false;
  }
  if (!ok) {
    loading = false;
    return;
  }
  resetAlert("alert");
  fetch("api/login.php", {
    method: "POST",
    // when passing data to php via js, don't use json because php $_POST doesn't read that (there is a workaround to reading json
    // in php, but lets just stick with this m'kay?)
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      username: uname,
      password: pass,
      rememberMe: rememberMe,
    }),
  })
    .then((res) => res.json())
    .catch((e) => console.log(e))
    .then((user) => {
      console.log("user is: " + JSON.stringify(user));
      // show error on #alert if invalid
      if (user.error) {
        addAlert("alert", user.error);
        loading = false;
        return;
      }
      // redirect to dashboard
      location.href = "dashboard.php";
    })
    .catch((e) => console.log(e));
  loading = false;
});

// show/hide in password field
document.getElementById("passEye").addEventListener("click", function () {
  console.log("eye toggled");
  const passwordInput = document.getElementById("password");
  const icon = this.children[0];

  // Toggle the input type
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    passwordInput.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
});
