"use strict";

$("#user_dataTable").DataTable({
  language: {
    emptyTable: "No users yet",
  },
  columns: [
    { data: "Fullname" },
    { data: "Username" },
    { data: "Role" },
    { data: "Barangay" },
    { data: "Action" },
  ],
  data: $("#user-table-body").is(":empty") ? [] : null,
});

// handle user delete btn
$(".delete-user-btn").on("click", async (e) => {
  if (loading) return;
  toggleLoading();

  const shouldDelete = await showConfirmationDialog(
    "Are you sure you want to delete this user?",
    "No",
    "Yes",
  );
  if (shouldDelete) {
    // use currenttarget instead of target, ot use closest()
    const userID = $(e.currentTarget).data("id");
    console.log("ID to remove:" + userID);
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
  toggleLoading();
});
