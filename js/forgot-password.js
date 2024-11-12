// Password reset functionality
document
  .getElementById("resetPasswordForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const emailInput = document.getElementById("email");
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    // Check if the email exists in localStorage
    const storedDoctor = JSON.parse(
      localStorage.getItem("doctor_" + emailInput.value)
    );
    if (!storedDoctor) {
      alert("No account found with this email. Please register.");
      return;
    }

    // Validate that passwords match
    if (newPassword !== confirmPassword) {
      document.getElementById("confirmPassword").classList.add("is-invalid");
    } else {
      document.getElementById("confirmPassword").classList.remove("is-invalid");

      // Update the doctor's password in localStorage
      storedDoctor.password = newPassword;
      localStorage.setItem(
        "doctor_" + emailInput.value,
        JSON.stringify(storedDoctor)
      );

      alert("Password successfully updated!");
      window.location.href = "index.html"; // Redirect to the login page
    }
  });
