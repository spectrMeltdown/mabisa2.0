"use strict";

const modalLabel = document.getElementById("modalLabel");
const passLabel = document.getElementById("passwordLabel");
let origPassLabel = "Password";
let origModalLabel = "Add user";
let currentUserID;
const defaultAlert = '<div class="alert"></div>';
// import $ from 'jquery';
// stopped using jquery because i'm practicing javascript lol

// for adding user
// listen when the admin changes selection, and display additional inputs
document.querySelector("#role").addEventListener("change", (event) => {
  console.log("Changed role");
  const selectedOption = event.target.value;
  const barangayDivSelector = document.querySelector("#barangayDiv");
  const barangay = document.querySelector("#barangay");
  // console.log(`${selectedOption}`);
  if (selectedOption == 2) {
    barangayDivSelector.style.display = "block";
    barangay.setAttribute("required", "true");
  } else {
    barangayDivSelector.style.display = "none";
    barangay.removeAttribute("required");
  }
});

// show/hide in password field
document.addEventListener("DOMContentLoaded", (e) => {
  document.getElementById("passEye").addEventListener("click", function () {
    console.log("eye toggled");
    const passwordInput = document.getElementById("pass");
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

  // show/hide password in confirm pass field
  document
    .getElementById("confirmPassEye")
    .addEventListener("click", function () {
      console.log("eye toggled (confirm pass)");
      const passwordInput = document.getElementById("confirmPass");
      const icon = this.children[0];
      console.log(icon.classList);
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
});

// for showing modal after clicking edit btn
// using document.addEventListener is better than document.querySelectorAll().forEach()
// because the latter only works when the element in query is static (not dynamically added/removed)
// reset the form element in crud user dialog
$("#crud-user").on("show.bs.modal", function (event) {
  if ($(event.relatedTarget).hasClass("edit-user-btn")) {
    console.log("editing");
    // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
    origModalLabel = modalLabel.textContent;
    modalLabel.textContent = "Edit User"; // Update the modal label text

    // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
    origPassLabel = passLabel.textContent;
    passLabel.textContent = "New Password"; // Update the modal label text

    // hide confirm password because we are editing
    $("#confirmPassField").css("display", "none");

    // // Show the edit modal crud-user
    // const modal = new bootstrap.Modal(document.getElementById('crud-user'));
    // modal.show();

    currentUserID = $(event.relatedTarget).data("id"); // Get the user ID from the clicked button

    // Show loading spinner and hide content initially
    // document.getElementById('loadingSpinner').style.display = 'block';
    // document.getElementById('modal-content').style.display = 'none';

    // GET request to ../api/get_user.php
    fetch(`../api/get_user.php?id=${currentUserID}`)
      .then((response) => response.json())
      .then((data) => {
        // Assuming 'data' contains the user info for filling the modal
        // console.log('Data is: ' + JSON.stringify(data));
        const user = data; // Assuming the response is an array, and we take the first user
        // Fill in the form inputs with the response data
        document.getElementById("fullName").value = user.fullName || "";
        document.getElementById("username").value = user.email || "";
        document.getElementById("email").value = user.email || "";
        document.getElementById("mobileNum").value =
          `+63${user.mobileNo}` || "";

        // Handle the 'role' select element
        const roleSelect = document.getElementById("role");
        const roleOptions = roleSelect.options;
        for (let i = 0; i < roleOptions.length; i++) {
          if (roleOptions[i].text === "Select one") {
            roleSelect.remove(i); // Remove the 'Select one' option
            break;
          }
        }
        // Add selected attribute to the matching role
        const roleOption = Array.from(roleSelect.options).find(
          (option) => option.text === user.role,
        );
        if (roleOption) {
          roleOption.selected = true;
        }

        // Handle the 'barangay' select element
        const barangaySelect = document.getElementById("barangay");
        const barangayDiv = document.getElementById("barangayDiv");
        console.log(`barangay was: ${user.barangay}`);
        if (user.barangay && user.barangay !== "N/A") {
          barangayDiv.style.display = "inline-block";
          const barangayOption = Array.from(barangaySelect.options).find(
            (option) => option.text === user.barangay,
          );
          if (barangayOption) {
            barangaySelect.remove(barangaySelect.selectedIndex); // Remove the 'Select one' option
            barangayOption.selected = true;
          }
        } else {
          // If barangay is null or 'N/A', remove 'Select one' option
          const barangaySelectOption = Array.from(barangaySelect.options).find(
            (option) => option.text === "Select one",
          );
          if (barangaySelectOption) {
            barangaySelect.remove(barangaySelectOption.index);
          }
        }

        // Hide the loading spinner and show the modal content
        document.getElementById("loadingSpinner").classList.remove("d-flex");
        document.getElementById("loadingSpinner").classList.add("d-none");
        document.getElementById("modal-content").classList.remove("d-none");
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        // Handle any errors (e.g., show an error message)
        document.getElementById("loadingSpinner").classList.remove("d-flex");
        document.getElementById("loadingSpinner").classList.add("d-none");
        document.getElementById("modal-content").classList.remove("d-none");
      });
  } else if ($(event.relatedTarget).hasClass("add-user-btn")) {
    console.log("adding");
    // Set default value to +63 on page load
    phoneInput.value = "+63";
    // Hide the loading spinner and show the modal content
    document.getElementById("loadingSpinner").classList.remove("d-flex");
    document.getElementById("loadingSpinner").classList.add("d-none");
    document.getElementById("modal-content").classList.remove("d-none");
    // Show the edit modal crud-user
  }
});

// reset the form element in crud user dialog
$("#crud-user").on("hidden.bs.modal", function (e) {
  console.log("modal hidden");
  const form = this.querySelector("form");
  if (form) form.reset();

  // revert the text to original
  modalLabel.textContent = origModalLabel;
  passLabel.textContent = origPassLabel;

  // hide barangay option
  const barangayDiv = document.getElementById("barangayDiv");
  barangayDiv.style.display = "none";

  // display confirm pass again
  $("#confirmPassField").css("display", "inline-block");

  // remove the userID
  currentUserID = undefined;

  //hide alerts
  $("#alert").html(defaultAlert);
});

// when submitting the form
$("#save-user-btn").on("click", async (e) => {
  // get all users to compare existing usernames & emails
  const users = await fetch(`../api/get_users.php`).then((response) => {
    if (!response.ok) {
      $("#alert").html(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          "<strong>Error!</strong> Network error!." +
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
          '<span aria-hidden="true">&times;</span>' +
          "</button>" +
          "</div>",
      );
      throw new Error("Network problem!");
    }
    return response.json();
  });
  let ok = true;
  const editMode = Boolean($(this).data("edit-mode"));
  e.preventDefault();

  console.log($("#username").val());
  // get input values
  const username = $("#username").val()?.trim();
  const fullName = $("#fullName").val()?.trim();
  const email = $("#email").val()?.trim();
  const mobileNum = $("#mobileNum").val()?.trim();
  const role = $("#role").val()?.trim();
  const barangay = $("#barangay").val()?.trim();
  const confirmPass = $("#confirmPass").val()?.trim();
  const password = $("#pass").val()?.trim();

  // check if password is long enough
  if (password.length < 8) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Password must at least 8 letters or numbers." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  // check if passwords match
  if (!editMode && password !== confirmPass) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Passwords do not match." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (username == "" || username == null) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Username cannot be empty." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (!editMode && users.some((user) => user.username === username)) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Username already taken." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (!editMode && users.some((user) => user.email === email)) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Email already taken." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (validateEmail(email) == false) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Invalid Email format." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (email == "" || email == null) {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Email cannot be empty." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if ((barangay == "" || barangay == null) && role === "Secretary") {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Barangay cannot be empty." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (role == null || role == "Select one") {
    $("#alert").html(
      '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        "<strong>Error!</strong> Please select a role." +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        "</button>" +
        "</div>",
    );
    ok = false;
  }

  if (!ok) {
    console.log("form not ok!");
    return;
  }
  $("#alert").html(defaultAlert);
  $.ajax({
    type: "POST",
    url: "../api/create_user.php",
    data: {
      username: username,
      fullName: fullName,
      email: email,
      mobileNum: mobileNum,
      pass: password,
      role: role,
      barangay: barangay,
    },
    success: function (result) {
      // check if null, empty, false, 0, infinity, etc
      if (!result) {
        $("#crud-user").modal("hide");
        location.reload();
        $("#main-toast-container").append(
          addToast("Success!", "User created successfully."),
        );
      } else {
        console.log("error!: " + result);
      }
    },
  });
});

const phoneInput = document.getElementById("mobileNum");
// Ensure the input always starts with +63
phoneInput.addEventListener("input", function (e) {
  if (!this.value.startsWith("+63")) {
    this.value = "+63" + this.value.slice(3); // Re-add +63 if it's removed
  }
});
// Optional: Prevent cursor from jumping to the start of the input
phoneInput.addEventListener("focus", function () {
  if (this.selectionStart < 3) {
    this.setSelectionRange(3, 3); // Set cursor after +63
  }
});
// Ensure typing starts after +63
phoneInput.addEventListener("keydown", function (e) {
  if (
    this.selectionStart < 3 &&
    e.key !== "ArrowRight" &&
    e.key !== "ArrowLeft"
  ) {
    e.preventDefault(); // Prevent modifying +63
    this.setSelectionRange(3, 3); // Move cursor after +63
  }
});

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// handle user delete btn
$(".delete-user-btn").on("click", async (e) => {
  const shouldDelete = await showConfirmationDialog();
  if (shouldDelete) {
    const userID = $(e.target).data("id");
    $.ajax({
      type: "POST",
      url: "../api/delete_user.php",
      data: {
        id: userID,
      },
      success: function (result) {
        if (!result) {
          $("#crud-user").modal("hide");
          location.reload();
          $("#main-toast-container").append(
            addToast("Success!", "User created successfully."),
          );
        } else {
          console.log("error!: " + result);
        }
      },
    });
  }
});
