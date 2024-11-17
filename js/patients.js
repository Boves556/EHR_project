function loadPatients() {
  const patientList = document.getElementById("patientList");

  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  if (!loggedInDoctor) {
    window.location.href = "index.html"; // Redirect if not logged in
    return;
  }

  // Retrieve the list of patients for the logged-in doctor
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];

  // Display patients or show a message if none are found
  if (patients.length === 0) {
    patientList.innerHTML = "<tr><td colspan='5'>No patients found.</td></tr>";
  } else {
    displayPatients(patients);
  }
}

function displayPatients(patients) {
  const patientList = document.getElementById("patientList");
  patientList.innerHTML = ""; // Clear the current list

  patients.forEach((patient, index) => {
    const row = document.createElement("tr");

    row.innerHTML = `
  <td>${patient.firstName} ${patient.lastName}</td>
  <td>${calculateAge(patient.dob)} years</td>
  <td>${patient.gender}</td>
  <td>${patient.country || "N/A"}</td>
  <td>
    <a href="ehr-details.html?patient=${index}" class="btn btn-gradient-purple">View EHR</a>
  </td>
`;

    patientList.appendChild(row);
  });
}

// Calculate age based on date of birth
function calculateAge(dob) {
  if (!dob) return "N/A";

  const [day, month, year] = dob.split("-");
  const birthDate = new Date(year, month - 1, day);
  const ageDiffMs = Date.now() - birthDate.getTime();
  const ageDate = new Date(ageDiffMs);
  return Math.abs(ageDate.getUTCFullYear() - 1970);
}

function sortPatients(order) {
  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  let patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];

  // Sort patients by name
  patients.sort((a, b) => {
    const nameA = (a.firstName + " " + a.lastName).toLowerCase();
    const nameB = (b.firstName + " " + b.lastName).toLowerCase();
    return order === "asc"
      ? nameA.localeCompare(nameB)
      : nameB.localeCompare(nameA);
  });

  displayPatients(patients);
}

function searchPatients() {
  const searchInput = document
    .getElementById("searchInput")
    .value.toLowerCase();
  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];

  // Filter patients by search input
  const filteredPatients = patients.filter((patient) => {
    const fullName = (patient.firstName + " " + patient.lastName).toLowerCase();
    return fullName.includes(searchInput);
  });

  displayPatients(filteredPatients);
}

// Load patients when the page is loaded
window.onload = loadPatients;
