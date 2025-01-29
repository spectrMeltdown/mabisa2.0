document.getElementById("barangaySelectBtn").addEventListener("click", (e) => {
  // show the dialog
});

document
  .getElementById("barangaySelectorDialog")
  .addEventListener("show.bs.modal", (e) => {
    // fix the css backdrop on the first modal when second modal is being overlayed
    $(".modal-backdrop")
      .last()
      .css(
        "z-index",
        parseInt($(".modal-backdrop").last().css("z-index")) + 10,
      );
    $(this).css("z-index", parseInt($(this).css("z-index")) + 10);

    // elements in the dialog
    const loading = document.getElementById("barSelectorLoadingSpinner");
    const list = document.getElementById("barangaySelectList");
    const none = document.getElementById("barSelectNoneText");

    // get available user_assignments and populate the checklist
    // fetch("../api/user_assignments.php", {
    //   method: "POST",
    //   headers: {
    //     "Content-Type": "application/x-www-form-urlencoded",
    //   },
    // })
    //   .then((res) => {
    //     if (!res.ok) {
    //       console.error(res.text);
    //       throw "Error loading.";
    //     }
    //     return res.json();
    //   })
    //   .catch((e) => {
    //     console.error(e);
    //     throw e;
    //   })
    //   .then((data) => {
    //     if (data && data.error) {
    //       addAlert(data.error);
    //       return;
    //     }
    //     loading.style.display = "none";
    //     console.log("Data is: " + JSON.stringify(data));
    //     if (data.length === 0) {
    //       none.style.display = "inline-block";
    //     } else {
    //       for (let entry in data) {
    //         const row = document.createElement("li");
    //         row.classList.add(
    //           "list-group-item",
    //           "d-flex",
    //           "justify-content-between",
    //           "align-items-center",
    //         );
    //         row.id = entry.brgyid ?? "--";
    //         row.textContent = entry.brgyname ?? "--";
    //         list.append(row);
    //       }
    //       list.style.display = "block";
    //     }
    //   });
  });
