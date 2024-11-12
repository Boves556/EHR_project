// Initialize Date Picker
$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });

  // Handle patient form submission
  $("#ehrForm").on("submit", function (e) {
    e.preventDefault(); // Prevent page reload on form submit

    const firstName = $("#firstName").val();
    const lastName = $("#lastName").val();
    const dob = $("#dob").val();
    const gender = $("input[name='gender']:checked").val();
    const country = $("#Country").val();

    // Create a new patient object
    const newPatient = {
      firstName,
      lastName,
      dob,
      gender,
      country,
    };

    // Retrieve the logged-in doctor information and patient list
    const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
    if (!loggedInDoctor) {
      alert("Doctor not logged in. Redirecting to login.");
      window.location.href = "index.html";
      return;
    }

    const patientsKey = "patients_" + loggedInDoctor.email;
    const patients = JSON.parse(localStorage.getItem(patientsKey)) || [];

    // Add the new patient to the list and save to local storage
    patients.push(newPatient);
    localStorage.setItem(patientsKey, JSON.stringify(patients));

    alert("Patient added successfully!");

    // Redirect to the patient list page after adding
    window.location.href = "ehr-details.html";
  });
});
