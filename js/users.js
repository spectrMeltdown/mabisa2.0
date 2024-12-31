"use strict"

// ensure dom is done loading
document.addEventListener("DOMContentLoaded", function(event) {

  // listen when the admin changes selection, and display additional inputs
  document.querySelector("#role").addEventListener("change", (event) => {
    const selectedOption = event.target.value;
    // console.log(`${selectedOption}`);
    const barangayDivSelector = document.querySelector("#barangayDiv");
    const barangay = document.querySelector("#barangay");
    if (selectedOption == 2) {
      barangayDivSelector.style.display = "block";
      barangay.setAttribute("required", "true");
    } else {
      barangayDivSelector.style.display = "none";
      barangay.removeAttribute("required");
    }
  });

  
});