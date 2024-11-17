// Email format validation on login form submission
document
  .getElementById("loginForm")
  .addEventListener("submit", function (event) {
    const emailInput = document.getElementById("email");
    const emailValue = emailInput.value;

    // Basic regex for email format validation
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegex.test(emailValue)) {
      event.preventDefault(); // Prevent form submission
      emailInput.classList.add("is-invalid"); // Show error feedback
    } else {
      emailInput.classList.remove("is-invalid"); // Remove error feedback
    }
  });

// Show/hide password functionality
document.getElementById("showPassword").addEventListener("click", function () {
  const passwordInput = document.getElementById("password");
  passwordInput.type = this.checked ? "text" : "password";
});
