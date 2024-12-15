$(document).ready(function () {
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });

  // Save button event
  document
    .getElementById("savePatientInfo")
    ?.addEventListener("click", function () {
      document.getElementById("formAction").value = "save";
      document.getElementById("patientInfoForm").submit();
    });

  // Delete button event
  document
    .getElementById("deletePatientInfo")
    ?.addEventListener("click", function () {
      if (confirm("Are you sure you want to delete this patient?")) {
        document.getElementById("formAction").value = "delete";
        document.getElementById("patientInfoForm").submit();
      }
    });
});

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
