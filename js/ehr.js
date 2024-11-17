// Initialize Date Picker
$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });

  // Patient Form Submission Logic
  document
    .getElementById("ehrForm")
    ?.addEventListener("submit", function (event) {
      event.preventDefault();
      const firstName = document.getElementById("firstName").value;
      const lastName = document.getElementById("lastName").value;
      const dob = document.getElementById("dob").value;
      const gender = document.querySelector(
        'input[name="gender"]:checked'
      ).value;
      const country = document.getElementById("Country").value;
      const loggedInDoctor = JSON.parse(
        localStorage.getItem("logged_in_doctor")
      );

      if (loggedInDoctor) {
        const patientsKey = "patients_" + loggedInDoctor.email;
        const patients = JSON.parse(localStorage.getItem(patientsKey)) || [];
        patients.push({ firstName, lastName, dob, gender, country });
        localStorage.setItem(patientsKey, JSON.stringify(patients));
        alert("Patient added successfully!");
        window.location.href = "patients.html";
      } else {
        alert("Please log in to add a patient.");
      }
    });
});
