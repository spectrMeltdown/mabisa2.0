"use strict";
// get user data onload
async () => {
  const user = await fetch("../api/get_user.php?id=self").then((res) =>
    res.json(),
  );

  // assign user data to display fields
  $("#username").text = user["username"];
  $("#email").text = user["email"];
  $("#fullName").text = user["fullName"];
  $("#mobileNum").text = user["mobileNum"];

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
};

// clicking the change profile pic button
document.getElementById("changePicBtn").addEventListener("click", () => {
  if (loading) return;
  loading = true;

  const fileInput = document.getElementById("fileInput");
  fileInput.value = ""; // Clear previous selection
  fileInput.click(); // Trigger the file selector
  loading = false;
});

// to change the file input
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
