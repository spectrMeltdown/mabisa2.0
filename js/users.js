"use strict"
// import $ from 'jquery';
// stopped using jquery because i'm practicing javascript lol

// for adding user 
// listen when the admin changes selection, and display additional inputs
document.querySelector("#role").addEventListener("change", (event) => {
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
document.getElementById('passEye').addEventListener('click', function () {
  const passwordInput = document.getElementById('pass');
  const icon = this.querySelector('i');
  
  // Toggle the input type
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});

// show/hide password in confirm pass field
document.getElementById('confirmPassEye').addEventListener('click', function () {
  const passwordInput = document.getElementById('confirmPass');
  const icon = this.querySelector('i');
  
  // Toggle the input type
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
});


// for showing modal after clicking edit btn
// using document.addEventListener is better than document.querySelectorAll().forEach() 
// because the latter only works when the element in query is static (not dynamically added/removed)
document.addEventListener('click', async (event) => {
  if (event.target.classList.contains('edit-user-btn')) {

    // Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
    const modalLabel = document.getElementById('modalLabel');
    const origModalLabel = modalLabel.textContent;
    modalLabel.textContent = 'Edit User';  // Update the modal label text

// Change the h5 with id 'modalLabel' under the modal with id="crud-user" to "Edit user"
const passLabel = document.getElementById('passwordLabel');
const origPassLabel = passLabel.textContent;
passLabel.textContent = 'New Password';  // Update the modal label text

    // // Show the edit modal crud-user
    // const modal = new bootstrap.Modal(document.getElementById('crud-user'));
    // modal.show();

    const userID = event.target.dataset.id;  // Get the user ID from the clicked button

    // Show loading spinner and hide content initially
    // document.getElementById('loadingSpinner').style.display = 'block';
    // document.getElementById('modal-content').style.display = 'none';

    // GET request to ../api/get_user.php
    await fetch(`../api/get_user.php?id=${userID}`)
    .then(response => response.json())
    .then(data => {
      // Assuming 'data' contains the user info for filling the modal
      // console.log('Data is: ' + JSON.stringify(data));
      const user = data; // Assuming the response is an array, and we take the first user
      // Fill in the form inputs with the response data
      document.getElementById('fullName').value = user.fullName || '';
      document.getElementById('username').value = user.email  || '';
      document.getElementById('email').value = user.email  || '';
      document.getElementById('mobileNum').value = user.mobileNo  || '';
      document.getElementById('pass').value = user.password  || '';
      document.getElementById('confirmPass').value = user.password  || '';

      // Handle the 'role' select element
      const roleSelect = document.getElementById('role');
      const roleOptions = roleSelect.options;
      for (let i = 0; i < roleOptions.length; i++) {
        if (roleOptions[i].text === 'Select one') {
          roleSelect.remove(i);  // Remove the 'Select one' option
          break;
        }
      }
      // Add selected attribute to the matching role
      const roleOption = Array.from(roleSelect.options).find(option => option.text === user.role);
      if (roleOption) {
        roleOption.selected = true;
      }

      // Handle the 'barangay' select element
      const barangaySelect = document.getElementById('barangay');
      const barangayDiv = document.getElementById('barangayDiv');
      if (user.barangay && user.barangay !== 'N/A') {
        console.log(user.barangay);
        barangayDiv.style.display = 'inline-block';
        const barangayOption = Array.from(barangaySelect.options).find(option => option.text === user.barangay);
        if (barangayOption) {
          barangaySelect.remove(barangaySelect.selectedIndex);  // Remove the 'Select one' option
          barangayOption.selected = true;
        }
      } else {
        // If barangay is null or 'N/A', remove 'Select one' option
        const barangaySelectOption = Array.from(barangaySelect.options).find(option => option.text === 'Select one');
        if (barangaySelectOption) {
          barangaySelect.remove(barangaySelectOption.index);
        }
      }

      // Hide the loading spinner and show the modal content
      document.getElementById('loadingSpinner').classList.remove('d-flex');
      document.getElementById('loadingSpinner').classList.add('d-none');
      document.getElementById('modal-content').classList.remove('d-none');
    })
    .catch(error => {
      console.error('Error fetching data:', error);
      // Handle any errors (e.g., show an error message)
      document.getElementById('loadingSpinner').classList.remove('d-flex');
      document.getElementById('loadingSpinner').classList.add('d-none');
      document.getElementById('modal-content').classList.remove('d-none');
    });
  }
  else if (event.target.classList.contains('.add-user-btn')) {
    // Show the edit modal crud-user
    const modal = new bootstrap.Modal(document.getElementById('crud-user'));
    modal.show();
  }
});

$('#crud-user').on('hidden.bs.modal', function () {
  console.log('entered here');
})

// reset the form element in crud user dialog
// document.getElementById('crud-user').addEventListener('hidden.bs.modal', function () {
//   console.log('entered here');
//   const form = this.querySelector('form');
//   if (form) form.reset();
//   // revert the text to original
//   modalLabel.textContent = origModalLabel;
//   passLabel.textContent = origPassLabel;
// });