"use strict";

async () => {
  const user = await fetch("../api/get_user.php?id=self").then((res) =>
    res.json(),
  );

  $("#username").text = user["username"];
  $("#email").text = user["email"];
  $("#fullName").text = user["fullName"];
  $("#mobileNum").text = user["mobileNum"];

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
