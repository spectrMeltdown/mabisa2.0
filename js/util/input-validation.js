"use strict";
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mobileNumRegex = /^\+639\d{9}$/;

function validEmail(email) {
  return emailRegex.test(email);
}

function validMobileNum(num) {
  return mobileNumRegex.test(num);
}
