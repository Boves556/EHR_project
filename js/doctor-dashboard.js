document.addEventListener("DOMContentLoaded", function () {
  fetch("api.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to fetch patient count");
      }
      return response.json();
    })
    .then((data) => {
      const patientCountElement = document.getElementById("patientCount");
      if (data.patientCount !== undefined) {
        patientCountElement.textContent = data.patientCount;
      } else {
        patientCountElement.textContent = "Error fetching count";
      }
    })
    .catch((error) => {
      console.error("Error fetching patient count:", error);
      document.getElementById("patientCount").textContent = "Error";
    });
});

document.addEventListener("DOMContentLoaded", function () {
  // Fetch and display the current profile picture
  const profilePic = document.getElementById("doctorProfilePic");
  fetch("profile.php")
    .then((response) => {
      if (response.ok) {
        return response.blob();
      } else {
        throw new Error("No profile picture found.");
      }
    })
    .then((blob) => {
      const url = URL.createObjectURL(blob);
      profilePic.src = url;
    })
    .catch((error) => {
      console.error("Error fetching profile picture:", error);
    });

  // Handle profile picture upload
  const uploadInput = document.getElementById("uploadProfilePic");
  uploadInput.addEventListener("change", function () {
    const file = uploadInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("profilePicture", file);

    fetch("profile.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Reload the profile picture
          fetch("profile.php")
            .then((response) => response.blob())
            .then((blob) => {
              const url = URL.createObjectURL(blob);
              profilePic.src = url;
            });
        } else {
          alert("Error uploading profile picture: " + data.error);
        }
      })
      .catch((error) => {
        console.error("Error uploading profile picture:", error);
        alert("An error occurred while uploading the profile picture.");
      });
  });
});
