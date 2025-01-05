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
  fetch("login.php")
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
