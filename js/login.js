document.getElementById("email").addEventListener("input", function () {
  const emailInput = this;
  const emailValue = emailInput.value;

  // Basic regex for email format validation
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  if (!emailRegex.test(emailValue)) {
    emailInput.classList.add("is-invalid");
  } else {
    emailInput.classList.remove("is-invalid");
  }
});

// Show/hide password functionality
document.getElementById("showPassword").addEventListener("click", function () {
  const passwordInput = document.getElementById("password");
  passwordInput.type = this.checked ? "text" : "password";
});
