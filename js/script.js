// Centralized Login/Logout & Navigation Functions
window.addEventListener("DOMContentLoaded", function () {
  handleLoginLogoutButton();
  fetchPatientStatistics();
  displayEHRDetails();
});

function handleLoginLogoutButton() {
  const loginLogoutButton = document.getElementById("loginLogoutButton");
  const navbarLinks = document.getElementById("navbarLinks");

  if (!loginLogoutButton || !navbarLinks) return;

  // Fetch login status from the backend
  fetch("check_login.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.loggedIn && data.loggedInDoctor) {
        loginLogoutButton.innerText = "Logout";
        loginLogoutButton.classList.replace("btn-theme-primary", "btn-danger");
        loginLogoutButton.addEventListener("click", handleLogout);

        // Add "Dashboard" link if not already present
        if (!document.getElementById("dashboardLink")) {
          const dashboardLink = document.createElement("li");
          dashboardLink.classList.add("nav-item");
          dashboardLink.id = "dashboardLink";
          dashboardLink.innerHTML =
            '<a class="nav-link" href="doctor-dashboard.php">Dashboard</a>';
          navbarLinks.appendChild(dashboardLink);
        }

        // Add "Your Patients" link if not already present
        if (!document.getElementById("yourPatientsLink")) {
          const yourPatientsLink = document.createElement("li");
          yourPatientsLink.classList.add("nav-item");
          yourPatientsLink.id = "yourPatientsLink";
          yourPatientsLink.innerHTML =
            '<a class="nav-link" href="patients.php">Your Patients</a>';
          navbarLinks.appendChild(yourPatientsLink);
        }

        // Add "New Patient" link if not already present
        if (!document.getElementById("newPatientLink")) {
          const newPatientLink = document.createElement("li");
          newPatientLink.classList.add("nav-item");
          newPatientLink.id = "newPatientLink";
          newPatientLink.innerHTML =
            '<a class="nav-link" href="ehr.php">New Patient</a>';
          navbarLinks.appendChild(newPatientLink);
        }
      } else {
        loginLogoutButton.innerText = "Login";
        loginLogoutButton.classList.replace("btn-danger", "btn-theme-primary");
        loginLogoutButton.href = "login.php";

        // Remove dynamic links when logged out
        ["dashboardLink", "yourPatientsLink", "newPatientLink"].forEach(
          (linkId) => {
            const link = document.getElementById(linkId);
            if (link) link.remove();
          }
        );
      }
    })
    .catch((error) => console.error("Error fetching login status:", error));
}

function handleLogout(event) {
  event.preventDefault();

  // Logout via backend
  fetch("logout.php", { method: "POST" })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Logged out successfully.");
        window.location.href = "index.php";
      } else {
        alert("Error during logout. Please try again.");
      }
    })
    .catch((error) => console.error("Error during logout:", error));
}

// Fetch and Display Patient Statistics
function fetchPatientStatistics() {
  const patientCountElement = document.getElementById("patientCount");

  if (patientCountElement) {
    fetch("get_patient_statistics.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          patientCountElement.innerText = data.patientCount || "0";
        } else {
          patientCountElement.innerText = "Error loading statistics.";
        }
      })
      .catch((error) => {
        console.error("Error fetching statistics:", error);
        patientCountElement.innerText = "Error.";
      });
  }
}

// Display EHR Details
function displayEHRDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");

  if (patientIndex) {
    // Fetch the patient details from the backend
    fetch(`get_patient_details.php?patient=${patientIndex}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.patient) {
          const patient = data.patient;

          document.getElementById(
            "patientName"
          ).innerText = `${patient.firstName} ${patient.lastName}`;
          document.getElementById("patientDob").innerText = patient.dob;
          document.getElementById("patientGender").innerText = patient.gender;
          document.getElementById("patientCountry").innerText =
            patient.country || "N/A";
        }
      })
      .catch((error) =>
        console.error("Error fetching patient details:", error)
      );
  }
}
