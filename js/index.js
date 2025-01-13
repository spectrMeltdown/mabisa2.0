"use strict";
let loading = false;

$("#loginBtn").on("click", (e) => {
  console.log("login btn pressed");
  if (loading) return;
  loading = true;

  let ok = false;
  const uname = $("#username").val()?.trim();
  const pass = $("#password").val()?.trim();
  const rememberMe = $("#rememberMe").val()?.trim();

  if (!pass) {
    addAlert("alert", "Password cannot be empty.");
    ok = false;
  }
  if (!uname) {
    addAlert("alert", "Username cannot be empty.");
    ok = false;
  }
  if (!ok) {
    resetAlert("alert");
    return;
  }
  fetch("api/login.php", {
    method: "POST",
    body: JSON.stringify({
      username: uname,
      password: pass,
      rememberMe: rememberMe,
    }),
  })
    .then((res) => res.json())
    .then((user) => {
      // show error on #alert if invalid
      if (Object.values(user).includes("error")) {
        addAlert("alert", user.error);
        return;
      }
      //save id for reuse, with expiry of 7 days
      setCookie("id", user.id, 7);
      // redirect to dashboard
      location.href = "index_main.php";
    });
});
