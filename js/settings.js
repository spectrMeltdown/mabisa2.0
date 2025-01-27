"use strict";

// get user data onload
document.addEventListener("DOMContentLoaded", async (e) => {
  const user = await fetch("../api/get_user.php?id=self")
    .then((res) => res.json())
    .catch((e) => {
      console.log("Error!" + e);
      throw e;
    });

  // assign user data to display fields
  $("#username").text(user["username"]);
  $("#email").text(user["email"]);
  $("#fullName").text(user["fullName"]);
  $("#mobileNum").text("+63" + user["mobileNum"]);
  $("#profilePic").attr(
    "src",
    user["profilePic"] ? user["profilePic"] : "../img/undraw_profile_2.svg",
  );

  // register delete button event handler
  $("#deleteAccountBtn").on("click", async (e) => {
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
});

// clicking the change profile pic button
document.getElementById("changePicBtn").addEventListener("click", () => {
  if (loading) return;
  loading = true;

  const fileInput = document.getElementById("fileInput");
  fileInput.value = ""; // Clear previous selection
  fileInput.click(); // Trigger the file selector
  loading = false;
});

// hidden file input element
document
  .getElementById("fileInput")
  .addEventListener("change", async (event) => {
    const file = event.target.files[0];

    if (!file) {
      return; // No file selected
    }

    // Check file size (<= 2MB)
    if (file.size > 2 * 1024 * 1024) {
      document.getElementById("alertDiv").innerText =
        "File size must not exceed 2MB.";
      document.getElementById("alertDiv").style.display = "block";
      return;
    }

    const formData = new FormData();
    formData.append("profileImage", file);

    try {
      const response = await fetch("../api/profile_pic.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        const error = await response.text();
        document.getElementById("alertDiv").innerText = error;
        document.getElementById("alertDiv").style.display = "block";
        return;
      }

      // If successful, clear alert div
      document.getElementById("alertDiv").style.display = "none";
      alert("Profile picture updated successfully!");
    } catch (error) {
      document.getElementById("alertDiv").innerText =
        "An error occurred while uploading. Please try again.";
      document.getElementById("alertDiv").style.display = "block";
    }
  });

$("#mobileNum-edit").on("click", async (e) => {
  const value = await changeProfileSettingDialog(
    "mobileNum",
    "Change Mobile Number",
    "Enter new mobile number:",
  );
});
$("#fullName-edit").on("click", async (e) => {
  const value = await changeProfileSettingDialog(
    "fullName",
    "Change Full Name",
    "Enter new full name:",
  );
});
$("#username-edit").on("click", async (e) => {
  const value = await changeProfileSettingDialog(
    "username",
    "Change Username",
    "Enter new username:",
  );
});
$("#password-edit").on("click", async (e) => {
  const value = await changeProfileSettingDialog(
    "password",
    "Change Password",
    "Enter new password:",
  );
});
$("#email-edit").on("click", async (e) => {
  const value = await changeProfileSettingDialog(
    "password",
    "Change Email",
    "Enter new email:",
  );
});

// create a enum (basically immutable object because js doesn't have enums)
const settingValues = [
  "mobileNum",
  "fullName",
  "password",
  "email",
  "username",
];
const setting = {};
for (const val in settingValues) {
  setting[val] = val;
}
Object.freeze(setting);

function checkSetting(valToCheck) {
  for (const val in setting) {
    console.log("Val is " + val);
    console.log("Val to check is " + valToCheck);
    if (val == valToCheck) {
      return true;
    }
  }
  return false;
}

// show dialog to get new value, then return that value
async function changeProfileSettingDialog(setting, title, subtitle, type) {
  if (checkSetting(setting)) {
    throw new Error("Invalid setting!");
  }
  $("#changeProfileSettingTitle").text(title);
  $("#changeProfileSettingSubtitle").text(subtitle);
  $("#" + setting).attr("name", setting);

  switch (setting) {
    case "mobileNum":
      $("#" + setting).attr("type", "tel");
      $("#" + setting).attr("maxLength", "10");
      $("#" + setting).attr("pattern", "^\+?[0-9]*$");
      $("#" + setting).attr("inputmode", "numeric");
      $("#" + setting).attr("autocomplete", "tel");
      break;
    case "fullName":
      $("#" + setting).attr("type", "text");
      $("#" + setting).attr("maxLength", "100");
      $("#" + setting).removeAttr("pattern");
      $("#" + setting).removeAttr("inputmode");
      $("#" + setting).attr("autocomplete", "name");
      break;
    case "password":
      $("#" + setting).attr("type", "password");
      $("#" + setting).attr("maxLength", "100");
      $("#" + setting).removeAttr("pattern");
      $("#" + setting).removeAttr("inputmode");
      $("#" + setting).attr("autocomplete", "new-password");
      break;
    case "email":
      $("#" + setting).attr("type", "email");
      $("#" + setting).attr("maxLength", "100");
      $("#" + setting).removeAttr("pattern");
      $("#" + setting).removeAttr("inputmode");
      $("#" + setting).attr("autocomplete", "email");
      break;
    case "username":
      $("#" + setting).attr("type", "text");
      $("#" + setting).attr("maxLength", "100");
      $("#" + setting).removeAttr("pattern");
      $("#" + setting).removeAttr("inputmode");
      $("#" + setting).attr("autocomplete", "username");
      break;
  }

  const dialog = new bootstrap.Modal($("#changeProfileSettingDialog").get(0));
  dialog.show();
  return new Promise((resolve) => {
    // return value if button clicked
    $("#changeProfileSettingSubmit")
      .off("click")
      .on("click", (e) => {
        const formData = new FormData($("#settingFormData").get(0));
        resolve(formData);
        dialog.hide();
      });
    // end if closed
    dialog.on("hidden.bs.modal", (e) => {
      resolve(null);
    });
  });
}
