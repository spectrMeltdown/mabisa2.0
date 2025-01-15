"use strict";
let loading = false;

document
  .getElementById("logoutBtn")
  .addEventListener("click", async (event) => {
    if (loading) return;
    loading = true;
    const shouldLogout = await showConfirmationDialog(
      "Are you sure you want to logout?",
      "No",
      "Yes",
    );
    if (shouldLogout) {
      fetch(
        (location.href.includes("dashboard") ? "" : "../") + "api/logout.php",
      )
        .then(async (res) => {
          if (!res.ok) {
            try {
              // console.error(res.json());
            } catch (error) {
              // console.error(res.text());
            }
            throw "Error";
          }
          console.error(await res.text());
          // return await res.json();
        })
        .catch((e) => {
          console.error(e);
          throw e; //stop the chain
        })
        .then((data) => {
          if (data !== undefined && data.error) {
            console.log(data.error);
          } else {
            // location.href = "../index.php";
          }
        });
    }
    loading = false;
  });
