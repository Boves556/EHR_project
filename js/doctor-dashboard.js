// Ensure the doctor information is displayed on the dashboard page
const doctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
if (doctor) {
  document.getElementById("doctorName").textContent = doctor.fullName;
  document.getElementById("doctorSpecialization").textContent = doctor.specialization;
} else {
  window.location.href = "index.html"; // Redirect if not logged in
}

// Display patient count specific to the logged-in doctor
const patientCountElement = document.getElementById("patientCount");
const patients = JSON.parse(localStorage.getItem("patients_" + doctor.email)) || [];
patientCountElement.textContent = patients.length;