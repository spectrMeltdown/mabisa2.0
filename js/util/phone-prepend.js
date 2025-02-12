function addPhonePrepend(id = 'mobileNum') {
  let phoneInput = $('#' + id);
  // Set default value to +63 on page load = '+63';
  phoneInput.val('+63');
  // Ensure the input always starts with +63
  phoneInput.on('input', phoneInputListener);
  // Optional: Prevent cursor from jumping to the start of the input
  phoneInput.on('focus', preventCursorStart);
  // Ensure typing starts after +63
  phoneInput.on('keydown', typeAfterPrepend);
}

function typeAfterPrepend(e) {
  if (e.selectionStart < 3 && e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') {
    e.target.preventDefault(); // Prevent modifying +63
    e.target.setSelectionRange(3, 3); // Move cursor after +63
  }
}

function preventCursorStart(e) {
  if (e.target.selectionStart < 3) {
    e.target.setSelectionRange(3, 3); // Set cursor after +63
  }
}

function phoneInputListener(e) {
  if (!e.target.value.startsWith('+63')) {
    e.target.value = '+63' + e.target.value.slice(3); // Re-add +63 if it's removed
  }
}

function removePhonePrepend(id = 'mobileNum') {
  let element = $('#' + id);
  element.off('input', phoneInputListener);
  // Optional: Prevent cursor from jumping to the start of the input
  element.off('focus', preventCursorStart);
  // Ensure typing starts after +63
  element.off('keydown', typeAfterPrepend);

  // below doesn't work
  // const cloned = element.cloneNode(true); // clone and keep children
  // element.parentNode.replaceChild(cloned, element); // replace with clone (effectively removing event listeners)
}
