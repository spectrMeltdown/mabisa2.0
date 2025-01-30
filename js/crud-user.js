const modalLabel = document.getElementById("modalLabel");
const passLabel = document.getElementById("passwordLabel");
let origPassLabel = "Password";
let origModalLabel = "New user";
let barangayAssgn = []; // should contain list of brgy ids that will be assigned with auditor
let currentUserID;
const defaultAlert = '<div class="alert"></div>';
let editMode = undefined;

// for showing modal after clicking edit btn
// using document.addEventListener is better than document.querySelectorAll().forEach()
// because the latter only works when the element in query is static (not dynamically added/removed)
// reset the form element in crud user dialog
$("#crud-user").on("show.bs.modal", function (event) {
  if (loading) return;
  toggleLoading();

  if ($(event.relatedTarget).hasClass("edit-user-btn")) {
    // console.log("editing");
    editMode = true;

    // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
    origModalLabel = modalLabel.textContent;
    modalLabel.textContent = "Edit User"; // Update the modal label text

    // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
    origPassLabel = passLabel.textContent;
    passLabel.textContent = "New Password"; // Update the modal label text

    // hide confirm password because we are editing
    $("#confirmPassField").css("display", "none");
    currentUserID = $(event.relatedTarget).data("id"); // Get the user ID from the clicked button

    // GET request to ../api/get_user.php
    fetch(`../api/get_user.php?id=${currentUserID}`)
      .then(async (res) => {
        let text = await res.text();
        console.log(text);
        return JSON.parse(text);
      })
      .then((user) => {
        // Assuming 'data' contains the user info for filling the modal
        // console.log("Data is: " + JSON.stringify(user));
        // Fill in the form inputs with the response data
        document.getElementById("fullName").value = user.fullName || "";
        document.getElementById("username").value = user.username || "";
        document.getElementById("email").value = user.email || "";
        document.getElementById("mobileNum").value =
          `+63${user.mobileNum}` || "";

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
          roleSelect.value = roleOption.value;
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
        document
          .getElementById("crud-user-modal-content")
          .classList.remove("d-none");
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        // Handle any errors (e.g., show an error message)
        document.getElementById("loadingSpinner").classList.remove("d-flex");
        document.getElementById("loadingSpinner").classList.add("d-none");
        document
          .getElementById("crud-user-modal-content")
          .classList.remove("d-none");
      });
  } else if ($(event.relatedTarget).hasClass("add-user-btn")) {
    // console.log("adding");
    editMode = false;
    addPhonePrepend();
    // Hide the loading spinner and show the modal content
    document.getElementById("loadingSpinner").classList.remove("d-flex");
    document.getElementById("loadingSpinner").classList.add("d-none");
    document
      .getElementById("crud-user-modal-content")
      .classList.remove("d-none");
    // Show the edit modal crud-user
  }
  toggleLoading();
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

  // hide the additional fields per role
  toggleAuditor(false);
  toggleSecretary(false);
  auditorBarangays = [];

  //hide alerts
  $("#alert").html(defaultAlert);
});

// for adding user
// listen when the admin changes selection, and display additional inputs
if (document.getElementById("role")) {
  document.getElementById("role").addEventListener("change", (event) => {
    console.log("Changed role");
    if (loading) return;
    toggleLoading();

    let selectedOption = event.target.value;
    console.log(`${selectedOption}`);

    if (selectedOption == "Secretary") {
      toggleAuditor(false);
      toggleSecretary(true);
    } else if (selectedOption == "Auditor") {
      toggleAuditor(true);
      toggleSecretary(false);
      const loading = document.getElementById("barangayAssignmentsLoading");
      const list = document.getElementById("barangayAssignmentsList");
      const none = document.getElementById("noBarangayAssignments");

      if (editMode) {
        console.log("editing auditor");
        console.log(currentUserID);
        fetch("../api/user_assignments.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            id: currentUserID,
          }),
        })
          .then((res) => {
            if (!res.ok) {
              console.error(res.text);
              throw "Error loading.";
            }
            return res.json();
          })
          .catch((e) => {
            console.error(e);
            throw e;
          })
          .then((data) => {
            if (data && data.error) {
              addAlert(data.error);
              return;
            }
            loading.style.display = "none";
            console.log("Data is: " + JSON.stringify(data));
            if (data.length === 0) {
              none.style.display = "inline-block";
            } else {
              for (let entry in data) {
                const row = document.createElement("li");
                row.classList.add(
                  "list-group-item",
                  "d-flex",
                  "justify-content-between",
                  "align-items-center",
                );
                row.id = entry.brgyid ?? "--";
                row.textContent = entry.brgyname ?? "--";
                list.append(row);
              }
              list.style.display = "block";
            }
          });
      } else {
        console.log("new auditor");
        loading.style.display = "none";
        if (auditorBarangays) {
          for (let entry in auditorBarangays) {
            const row = document.createElement("li");
            row.classList.add(
              "list-group-item",
              "d-flex",
              "justify-content-between",
              "align-items-center",
            );
            row.id = entry.brgyid ?? "--";
            row.textContent = entry.brgyname ?? "--";
            list.append(row);
          }
          list.style.display = "block";
        } else {
          none.style.display = "block";
        }
      }
    }

    // admin
    else {
      toggleAuditor(false);
      toggleSecretary(false);
    }
    toggleLoading();
  });
}

// show/hide in password field
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

// when submitting the form
$("#save-user-btn").on("click", async (e) => {
  if (loading) return;
  toggleLoading();

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

  // console.log($("#username").val());
  // get input values
  const username = $("#username").val()?.trim();
  const fullName = $("#fullName").val()?.trim();
  const email = $("#email").val()?.trim();
  const mobileNum = $("#mobileNum").val()?.trim();
  const role = $("#role").val()?.trim();
  const barangay = $("#barangay").val()?.trim();
  const confirmPass = $("#confirmPass").val()?.trim();
  const password = $("#pass").val()?.trim();

  function resetFieldStates() {
    $("#username").removeClass("is-invalid");
    $("#username").find(".invalid-feedback").first().text("");
    $("#fullName").removeClass("is-invalid");
    $("#fullName").find(".invalid-feedback").first().text("");
    $("#email").removeClass("is-invalid");
    $("#email").find(".invalid-feedback").first().text("");
    $("#mobileNum").removeClass("is-invalid");
    $("#mobileNum").find(".invalid-feedback").first().text("");
    $("#role").removeClass("is-invalid");
    $("#role").find(".invalid-feedback").first().text("");
    $("#barangay").removeClass("is-invalid");
    $("#barangay").find(".invalid-feedback").first().text("");
    $("#confirmPass").removeClass("is-invalid");
    $("#confirmPass").find(".invalid-feedback").first().text("");
    $("#pass").removeClass("is-invalid");
    $("#pass").find(".invalid-feedback").first().text("");
  }

  function addError(element, message, customFeedbackElement) {
    if (customFeedbackElement) {
      element.find(".invalid-feedback").first().text(message);
    } else {
      element.next(".invalid-feedback").first().text(message);
    }
    element.addClass("is-invalid");
  }

  resetFieldStates();

  // PASSWORD
  if (!editMode && (password == "" || password == null)) {
    addError($("#pass"), "Password cannot be empty.", $("#passField"));
    ok = false;
  }
  if (!editMode && password.length < 8) {
    addError($("#pass"), "Password must be at least 8 characters.");
    ok = false;
  }
  if (!editMode && password.length > 100) {
    addError($("#pass"), "Password too long.");
    ok = false;
  }

  // CONFIRM PASS
  if (!editMode && (confirmPass == "" || confirmPass == null)) {
    addError(
      $("#confirmPass"),
      "Please type in the password again.",
      $("#confirmPassField"),
    );
    ok = false;
  }
  if (!editMode && password !== confirmPass) {
    addError($("#confirmPass"), "Passwords do not match.");
    ok = false;
  }

  // USERNAME
  if (username == "" || username == null) {
    addError($("#username"), "Username cannot be empty.");
    ok = false;
  }
  if (username.length > 100) {
    addError($("#pass"), "Username too long.");
    ok = false;
  }
  if (
    !editMode &&
    users.some((user) => user.username === username && user.id != currentUserID)
  ) {
    addError($("#username"), "Username already taken.");
    ok = false;
  }

  // FULLNAME
  if (fullName == "" || fullName == null) {
    addError($("#fullName"), "Name cannot be empty.");
    ok = false;
  }
  if (fullName.length > 100) {
    addError($("#fullName"), "Name too long.");
    ok = false;
  }

  // EMAIL
  if (!validEmail(email)) {
    addError($("#email"), "Invalid email.");
    ok = false;
  }
  if (
    !editMode &&
    users.some((user) => user.email === email && user.id != currentUserID)
  ) {
    addError($("#email"), "Email already taken.");
    ok = false;
  }
  if (email.length > 100) {
    addError($("#email"), "Email too long.");
    ok = false;
  }
  if (email == "" || email == null) {
    addError($("#email"), "Email cannot be empty.");
    ok = false;
  }

  // MOBILE NUMBER
  if (!validMobileNum(mobileNum)) {
    addError($("#mobileNum"), "Invalid mobile number.");
    ok = false;
  }
  if (mobileNum == "+63" || mobileNum == "" || mobileNum == null) {
    addError($("#mobileNum"), "Mobile number cannot be empty.");
    ok = false;
  }

  // BARANGAY
  if ((barangay == "" || barangay == null) && role === "Secretary") {
    addError($("#barangay"), "Please select a barangay.");
    ok = false;
  }

  // ROLE
  if (role == null || role == "Select one") {
    addError($("#role"), "Please select a role.");
    ok = false;
  }

  // FOR AUDITOR
  if (role === "Auditor" && auditorBarangays.length == 0) {
    addError($("#role"), "Please assign barangays to this auditor.");
    ok = false;
  }

  if (!ok) {
    console.log("form not ok!");
    toggleLoading();
    return;
  }

  $("#alert").html(defaultAlert);
  const formData = new FormData($("#crud-user-modal-content").get(0));
  // some extra work before submitting
  switch (role) {
    case "Admin":
      formData.delete("barangay");
      formData.delete("auditorBarangays");
      break;
    case "Secretary":
      formData.delete("auditorBarangays");
      break;
    case "Auditor":
      formData.delete("barangay");
      formData.append("auditorBarangays", JSON.stringify(auditorBarangays));
      break;
    default:
      console.error("Unknown role when saving the user!");
  }
  if (!editMode) {
    console.log("not edit mode");
    $.ajax({
      type: "POST",
      url: "../api/create_user.php",
      data: formData, // Use 'data' instead of 'body'
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false, // Prevent jQuery from setting content type
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
  } else {
    formData.append("id", currentUserID);
    $.ajax({
      type: "POST",
      url: "../api/edit_user.php",
      data: formData, // Use 'data' instead of 'body'
      processData: false, // Prevent jQuery from processing the FormData
      contentType: false, // Prevent jQuery from setting content type
      success: function (result) {
        // check if null, empty, false, 0, infinity, etc
        if (!result) {
          $("#crud-user").modal("hide");
          location.reload();
          $("#main-toast-container").append(
            addToast("Success!", "User successfully edited."),
          );
        } else {
          console.log("error!: " + result);
        }
      },
    });
  }
  toggleLoading();
});
