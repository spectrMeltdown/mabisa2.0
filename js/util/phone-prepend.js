function addPhonePrepend(id = 'mobileNum') {
  const phoneInput = document.getElementById(id);
  // Set default value to +63 on page load
  phoneInput.value = '+63';
  // Ensure the input always starts with +63
  phoneInput.addEventListener('input', function(e) {
    if (!this.value.startsWith('+63')) {
      this.value = '+63' + this.value.slice(3); // Re-add +63 if it's removed
    }
  });
  // Optional: Prevent cursor from jumping to the start of the input
  phoneInput.addEventListener('focus', function() {
    if (this.selectionStart < 3) {
      this.setSelectionRange(3, 3); // Set cursor after +63
    }
  });
  // Ensure typing starts after +63
  phoneInput.addEventListener('keydown', function(e) {
    if (
      this.selectionStart < 3 &&
      e.key !== 'ArrowRight' &&
      e.key !== 'ArrowLeft'
    ) {
      e.preventDefault(); // Prevent modifying +63
      this.setSelectionRange(3, 3); // Move cursor after +63
    }
  });
}

function removePhonePrepend(id = 'mobileNum') {
  const element = document.querySelector('#' + id);
  const cloned = element.cloneNode(true); // clone and keep children
  element.parentNode.replaceChild(cloned, element); // replace with clone (effectively removing event listeners)
}
