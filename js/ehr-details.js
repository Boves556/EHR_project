// Initialize the date picker
$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });
});

// Function to load specific patient details
function loadPatientDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");

  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patients =
    JSON.parse(localStorage.getItem("patients_" + loggedInDoctor.email)) || [];
  const patient = patients[patientIndex];

  if (patient) {
    // Load basic information
    document.getElementById("editPatientName").value =
      patient.firstName + " " + patient.lastName;
    document.getElementById("editAge").value = patient.dob || "";
    document.getElementById("editGender").value = patient.gender;
    document.getElementById("editCountry").value = patient.country || "";
    document.getElementById("editAppointments").value = patient.appointments
      ? patient.appointments.join("\n")
      : "";
    document.getElementById("appointmentDate").value =
      patient.appointmentDate || "";
    document.getElementById("doctorNotes").value = patient.doctorNotes || "";

    // Load patient-specific fields
    document.getElementById("mobile").value = patient.mobile || "";
    document.getElementById("email").value = patient.email || "";
    document.getElementById("otherPhone").value = patient.otherPhone || "";
    document.getElementById("insuranceNumber").value =
      patient.insuranceNumber || "";
    document.getElementById("address").value = patient.address || "";
    document.getElementById("postalCode").value = patient.postalCode || "";
    document.getElementById("billingAddress").value =
      patient.billingAddress || "";
    document.getElementById("amountToBePaid").value =
      patient.amountToBePaid || "";
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
    patient.country = document.getElementById("editCountry").value;
    patient.appointments = document
      .getElementById("editAppointments")
      .value.split("\n");
    patient.appointmentDate = document.getElementById("appointmentDate").value;
    patient.doctorNotes = document.getElementById("doctorNotes").value;

    // Save updated patient-specific fields
    patient.mobile = document.getElementById("mobile").value;
    patient.email = document.getElementById("email").value;
    patient.otherPhone = document.getElementById("otherPhone").value;
    patient.insuranceNumber = document.getElementById("insuranceNumber").value;
    patient.address = document.getElementById("address").value;
    patient.postalCode = document.getElementById("postalCode").value;
    patient.billingAddress = document.getElementById("billingAddress").value;
    patient.amountToBePaid = document.getElementById("amountToBePaid").value;

    // Save updated data to localStorage
    localStorage.setItem(
      "patients_" + loggedInDoctor.email,
      JSON.stringify(patients)
    );
    alert("All patient information updated successfully!");
  }
}

// Event listener for the Save button
document
  .getElementById("savePatientInfo")
  .addEventListener("click", savePatientDetails);

// Function to delete patient details
function deletePatientDetails() {
  const urlParams = new URLSearchParams(window.location.search);
  const patientIndex = urlParams.get("patient");

  const loggedInDoctor = JSON.parse(localStorage.getItem("logged_in_doctor"));
  const patientsKey = "patients_" + loggedInDoctor.email;
  const patients = JSON.parse(localStorage.getItem(patientsKey)) || [];

  if (patients[patientIndex]) {
    const isConfirmed = confirm(
      "Are you sure you want to delete this patient?"
    );
    if (isConfirmed) {
      patients.splice(patientIndex, 1);
      localStorage.setItem(patientsKey, JSON.stringify(patients));
      alert("Patient information has been deleted.");
      window.location.href = "patients.html"; // Redirect to the patient list page
    }
  } else {
    alert("Patient information not found.");
  }
}

// Event listener for the Delete button
document
  .getElementById("deletePatientInfo")
  .addEventListener("click", deletePatientDetails);

// Apply specific behavior for textboxes based on field type

// Horizontal input fields for specific fields
const horizontalFields = [
  "mobile",
  "email",
  "otherPhone",
  "insuranceNumber",
  "address",
  "postalCode",
  "billingAddress",
  "amountToBePaid",
];
horizontalFields.forEach((id) => {
  const field = document.getElementById(id);
  if (field) {
    field.setAttribute("rows", "1");
    field.style.height = "auto";
    field.style.overflow = "hidden";
    field.style.resize = "none"; // Disable resizing
    field.style.whiteSpace = "nowrap"; // Prevent line breaks
    field.style.textOverflow = "ellipsis"; // Handle overflow with ellipsis
    field.addEventListener("input", function () {
      this.value = this.value.replace(/\n/g, ""); // Prevent new lines
    });
  }
});

// Expandable vertical fields for medical notes
const expandableFields = [
  "symptoms",
  "familyHistory",
  "scanTests",
  "diagnosis",
  "medications",
  "labTests",
];
expandableFields.forEach((id) => {
  const field = document.getElementById(id);
  if (field) {
    field.setAttribute("rows", "1");
    field.style.height = "auto";
    field.style.overflowY = "hidden";
    field.style.resize = "none"; // Disable manual resizing
    field.addEventListener("input", function () {
      this.style.height = "auto"; // Reset height to calculate proper size
      const scrollHeight = this.scrollHeight; // Get full scroll height
      const maxRowsHeight = 3 * parseFloat(getComputedStyle(this).lineHeight); // Height for 3 rows
      if (scrollHeight > maxRowsHeight) {
        this.style.height = maxRowsHeight + "px"; // Limit height to 3 rows
        this.style.overflowY = "scroll"; // Enable scrolling for extra content
      } else {
        this.style.height = scrollHeight + "px"; // Adjust height to content
        this.style.overflowY = "hidden"; // Hide scroll if no overflow
      }
    });
  }
});

// Initialize image management functionality
document.addEventListener("DOMContentLoaded", function () {
  const imageContainer = document.getElementById("imageContainer");
  const uploadImageInput = document.getElementById("uploadImageInput");

  // Function to handle image uploads
  uploadImageInput.addEventListener("change", function () {
    const files = Array.from(this.files); // Convert FileList to an array
    files.forEach((file) => {
      const reader = new FileReader();
      reader.onload = function (e) {
        addImageToContainer(e.target.result); // Add uploaded image
      };
      reader.readAsDataURL(file); // Read file as Data URL
    });

    // Clear the input value to allow re-uploading the same file
    this.value = "";
  });

  // Function to add an image to the container
  function addImageToContainer(imageSrc) {
    const imageWrapper = document.createElement("div");
    imageWrapper.className = "position-relative";

    const img = document.createElement("img");
    img.src = imageSrc;
    img.alt = "Uploaded Image";
    img.className = "img-thumbnail";
    img.style.width = "600px";

    const deleteButton = document.createElement("button");
    deleteButton.className = "btn btn-danger btn-sm position-absolute";
    deleteButton.style.top = "5px";
    deleteButton.style.right = "5px";
    deleteButton.innerHTML = "&times;";
    deleteButton.addEventListener("click", function () {
      imageWrapper.remove(); // Remove specific image
    });

    imageWrapper.appendChild(img);
    imageWrapper.appendChild(deleteButton);
    imageContainer.appendChild(imageWrapper);
  }
});

window.onload = loadPatientDetails;
