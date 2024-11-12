// Centralized Login/Logout & Navigation Functions
window.addEventListener("DOMContentLoaded", function () {
  handleLoginLogoutButton();
  displayPatients();
  displayEHRDetails();
});

function handleLoginLogoutButton() {
  const loginLogoutButton = document.getElementById("loginLogoutButton");
  const navbarLinks = document.getElementById("navbarLinks");

  if (!loginLogoutButton) return;

  // Check if user is logged in
  const loggedIn = localStorage.getItem("loggedIn");
  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));

  if (loggedIn && loggedInDoctor) {
    loginLogoutButton.innerText = "Logout";
    loginLogoutButton.classList.replace("btn-teal", "btn-danger");
    loginLogoutButton.href = "#"; // Prevent redirect for logout
    loginLogoutButton.addEventListener("click", handleLogout);

    if (!document.getElementById("dashboardLink")) {
      const dashboardLink = document.createElement("li");
      dashboardLink.classList.add("nav-item");
      dashboardLink.id = "dashboardLink";
      dashboardLink.innerHTML =
        '<a class="nav-link" href="doctor-dashboard.html">Dashboard</a>';
      navbarLinks.appendChild(dashboardLink);
    }
  } else {
    loginLogoutButton.innerText = "Login";
    loginLogoutButton.classList.replace("btn-danger", "btn-teal");
    loginLogoutButton.href = "index.html";
  }
}

function handleLogout(event) {
  event.preventDefault();
  localStorage.removeItem("loggedIn");
  localStorage.removeItem("logged_in_doctor");
  alert("Logged out successfully.");
  window.location.href = "index.html";
}

// Registration Form Logic
document
  .getElementById("registrationForm")
  ?.addEventListener("submit", function (event) {
    event.preventDefault();
    const fullName = document.getElementById("fullName").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
      alert("Passwords do not match.");
      return;
    }

    localStorage.setItem(
      "doctor_" + email,
      JSON.stringify({ fullName, email, password })
    );
    alert("Registration successful! Please log in.");
    window.location.href = "index.html";
  });

// Login Form Logic
document
  .getElementById("loginForm")
  ?.addEventListener("submit", function (event) {
    event.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const storedDoctor = JSON.parse(localStorage.getItem("doctor_" + email));

    if (storedDoctor && storedDoctor.password === password) {
      localStorage.setItem("loggedIn", "true");
      localStorage.setItem("logged_in_doctor", JSON.stringify(storedDoctor));
      alert("Login successful!");
      window.location.href = "doctor-dashboard.html";
    } else {
      alert("Invalid credentials.");
    }
  });

// Patient Form Submission Logic
document
  .getElementById("ehrForm")
  ?.addEventListener("submit", function (event) {
    event.preventDefault();
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const dob = document.getElementById("dob").value;
    const gender = document.querySelector('input[name="gender"]:checked').value;
    const country = document.getElementById("Country").value;
    const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));

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

// Display Patients
function displayPatients() {
  const patientList = document.getElementById("patientList");
  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));

  if (patientList && loggedInDoctor) {
    const patients =
      JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) ||
      [];
    patients.sort((a, b) => a.firstName.localeCompare(b.firstName));

    patientList.innerHTML = patients.length
      ? patients
          .map(
            (p, i) =>
              `<tr><td>${p.firstName} ${p.lastName}</td><td>${
                p.gender
              }</td><td>${
                p.country || "N/A"
              }</td><td><a href="ehr-details.html?patient=${i}" class="btn btn-primary">View</a></td></tr>`
          )
          .join("")
      : "<tr><td colspan='5'>No patients found.</td></tr>";
  }
}

// Display EHR Details
function displayEHRDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");
  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];
  const patient = patients[patientIndex];

  if (patient) {
    document.getElementById(
      "patientName"
    ).innerText = `${patient.firstName} ${patient.lastName}`;
    document.getElementById("patientDob").innerText = patient.dob;
    document.getElementById("patientGender").innerText = patient.gender;
    document.getElementById("patientCountry").innerText =
      patient.country || "N/A";
  }
}
