document.addEventListener("DOMContentLoaded", function () {
  const imageContainer = document.getElementById("imageContainer");
  const uploadImageInput = document.getElementById("uploadImageInput");

  // Initialize datepicker
  $(".datepicker").datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
  });

  // Save button event
  const saveButton = document.getElementById("savePatientInfo");
  if (saveButton) {
    saveButton.addEventListener("click", function () {
      document.getElementById("formAction").value = "save";
      document.getElementById("patientInfoForm").submit();
    });
  }

  // Delete button event
  const deleteButton = document.getElementById("deletePatientInfo");
  if (deleteButton) {
    deleteButton.addEventListener("click", function () {
      if (confirm("Are you sure you want to delete this patient?")) {
        document.getElementById("formAction").value = "delete";
        document.getElementById("patientInfoForm").submit();
      }
    });
  }

  // Handle image upload functionality
  if (uploadImageInput) {
    uploadImageInput.addEventListener("change", function () {
      const files = Array.from(this.files);
      const formData = new FormData();
      formData.append("action", "upload_image");
      files.forEach((file) => formData.append("images[]", file));

      fetch("", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) throw new Error("Failed to upload images.");
          return response.json();
        })
        .then((data) => {
          if (data.success && data.uploadedData) {
            data.uploadedData.forEach((fileObj) => {
              addImageToContainer(
                fileObj.base64,
                fileObj.imageId,
                fileObj.fileName
              );
            });
          } else {
            alert("Failed to upload images.");
          }
        })
        .catch((error) => {
          console.error("Upload error:", error);
          alert("An error occurred during upload. Please try again.");
        });

      // Clear the input value to allow re-uploads
      this.value = "";
    });
  }

  // Function to add an image (base64) to the container
  function addImageToContainer(base64String, imageId, fileName) {
    if (!imageId || !base64String) {
      console.error("Invalid image data:", { imageId, base64String });
      return;
    }

    const imageWrapper = document.createElement("div");
    imageWrapper.className = "position-relative";

    const img = document.createElement("img");
    img.src = "data:image/jpeg;base64," + base64String;
    img.alt = fileName || "Uploaded Image";
    img.className = "img-thumbnail";
    img.style.width = "500px";

    const deleteButton = document.createElement("button");
    deleteButton.className = "btn btn-danger btn-sm position-absolute";
    deleteButton.style.top = "5px";
    deleteButton.style.right = "5px";
    deleteButton.innerHTML = "&times;";
    deleteButton.addEventListener("click", function () {
      deleteImage(imageId, imageWrapper);
    });

    imageWrapper.appendChild(img);
    imageWrapper.appendChild(deleteButton);
    imageContainer.appendChild(imageWrapper);
  }

  // Function to delete an image from the server and UI
  function deleteImage(imageId, imageWrapper) {
    if (!imageId) {
      console.error("Invalid image ID:", imageId);
      alert("An error occurred while attempting to delete the image.");
      return;
    }

    if (confirm("Are you sure you want to delete this image?")) {
      const formData = new FormData();
      formData.append("action", "delete_image");
      formData.append("image_id", imageId);

      fetch("", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) throw new Error("Failed to delete image.");
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            imageWrapper.remove();
          } else {
            alert("Failed to delete image. Please try again.");
          }
        })
        .catch((error) => {
          console.error("Delete error:", error);
          alert("An error occurred while deleting the image.");
        });
    }
  }

  // Single-line fields (no newlines allowed)
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
      field.style.resize = "none";
      field.style.whiteSpace = "nowrap";
      field.style.textOverflow = "ellipsis";
      field.addEventListener("input", function () {
        // Prevent new lines in single-line fields
        this.value = this.value.replace(/\n/g, "");
      });
    }
  });

  // Multi-line, expandable fields (preserve new lines)
  // Multi-line, expandable fields (preserve new lines)
  const expandableFields = [
    "symptoms",
    "familyHistory",
    "scanTests",
    "diagnosis",
    "medications",
    "labTests",
    "doctorNotes",
  ];

  expandableFields.forEach((id) => {
    const field = document.getElementById(id);
    if (field) {
      field.style.height = "auto";
      field.style.overflowY = "hidden";
      field.addEventListener("input", function () {
        // Ensure textarea auto-expands while retaining content newlines
        this.style.height = "auto";
        this.style.height = `${this.scrollHeight}px`;
      });

      // Replace visible newlines in field data if needed
      if (field.value) {
        field.value = field.value.replace(/\\n/g, "\n");
      }
    }
  });
});
