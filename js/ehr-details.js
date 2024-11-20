// Initialize the date picker
$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });
});

// Function to add additional information fields
function addInformationField() {
  const infoContainer = document.getElementById("infoContainer");

  const infoRow = document.createElement("div");
  infoRow.classList.add("mb-4");

  const titleLabel = document.createElement("label");
  titleLabel.textContent = "Title";
  const titleInput = document.createElement("input");
  titleInput.type = "text";
  titleInput.classList.add("form-control", "mb-2");
  titleInput.placeholder = "Enter title";

  const subtitleLabel = document.createElement("label");
  subtitleLabel.textContent = "Subtitle";
  const subtitleTextarea = document.createElement("textarea");
  subtitleTextarea.classList.add("form-control", "subtitle-textarea");
  subtitleTextarea.placeholder = "Enter subtitle information...";
  subtitleTextarea.rows = 5;
  subtitleTextarea.style.overflowX = "hidden"; // Prevent horizontal scrolling

  infoRow.appendChild(titleLabel);
  infoRow.appendChild(titleInput);
  infoRow.appendChild(subtitleLabel);
  infoRow.appendChild(subtitleTextarea);
  infoContainer.appendChild(infoRow);
}

document
  .getElementById("addInfoButton")
  .addEventListener("click", addInformationField);

// Function to load specific patient details
function loadPatientDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");

  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];
  const patient = patients[patientIndex];

  if (patient) {
    document.getElementById("editPatientName").value =
      patient.firstName + " " + patient.lastName;
    document.getElementById("editAge").value = patient.dob || "";
    document.getElementById("editGender").value = patient.gender;
    document.getElementById("editCountry").value = patient.country || ""; // Load country value
    document.getElementById("editAppointments").value = patient.appointments
      ? patient.appointments.join("\n")
      : "";
    document.getElementById("appointmentDate").value =
      patient.appointmentDate || "";
    document.getElementById("doctorNotes").value = patient.doctorNotes || "";

    if (patient.additionalInfo) {
      patient.additionalInfo.forEach((info) => {
        const infoRow = document.createElement("div");
        infoRow.classList.add("mb-4");

        const titleInput = document.createElement("input");
        titleInput.type = "text";
        titleInput.classList.add("form-control", "mb-2");
        titleInput.value = info.title;

        const subtitleTextarea = document.createElement("textarea");
        subtitleTextarea.classList.add("form-control", "subtitle-textarea");
        subtitleTextarea.value = info.subtitle;
        subtitleTextarea.rows = 5;
        subtitleTextarea.style.overflowX = "hidden";

        infoRow.appendChild(titleInput);
        infoRow.appendChild(subtitleTextarea);
        document.getElementById("infoContainer").appendChild(infoRow);
      });
    }
  } else {
    alert("No patient information found.");
  }
}

// Function to save updated patient details
function savePatientDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");

  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];

  if (patients[patientIndex]) {
    const patient = patients[patientIndex];
    const [firstName, ...lastName] = document
      .getElementById("editPatientName")
      .value.split(" ");
    patient.firstName = firstName;
    patient.lastName = lastName.join(" ");
    patient.dob = document.getElementById("editAge").value;
    patient.gender = document.getElementById("editGender").value;
    patient.country = document.getElementById("editCountry").value; // Save country value
    patient.appointments = document
      .getElementById("editAppointments")
      .value.split("\n");
    patient.appointmentDate = document.getElementById("appointmentDate").value;
    patient.doctorNotes = document.getElementById("doctorNotes").value;

    const additionalInfo = [];
    document.querySelectorAll("#infoContainer > div").forEach((infoRow) => {
      const title = infoRow.querySelector("input").value;
      const subtitle = infoRow.querySelector("textarea").value;
      additionalInfo.push({ title, subtitle });
    });
    patient.additionalInfo = additionalInfo;

    localStorage.setItem(
      "patients_" + loggedInDoctor.email,
      JSON.stringify(patients)
    );
    alert("All patient information updated successfully!");
  }
}

document
  .getElementById("savePatientInfo")
  .addEventListener("click", savePatientDetails);

window.onload = loadPatientDetails;

// Function to delete patient details
function deletePatientDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const patientIndex = urlParams.get("patient");
  
    // Get logged-in doctor's patients list
    const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
    const patientsKey = "patients_" + loggedInDoctor.email;
    const patients = JSON.parse(localStorage.getItem(patientsKey)) || [];
  
    if (patients[patientIndex]) {
      // Confirm deletion
      const isConfirmed = confirm("Are you sure you want to delete this patient?");
      if (isConfirmed) {
        // Remove the patient from the array and update localStorage
        patients.splice(patientIndex, 1);
        localStorage.setItem(patientsKey, JSON.stringify(patients));
  
        alert("Patient information has been deleted.");
        window.location.href = "patients.html"; // Redirect to the patient list page
      }
    } else {
      alert("Patient information not found.");
    }
  }
  
  // Event listener for the delete button
  document.getElementById("deletePatientInfo").addEventListener("click", deletePatientDetails);