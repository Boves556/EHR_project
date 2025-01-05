<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connection.php';
$doctor_id = $_SESSION['doctor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePicture'])) {
    // Check for upload errors
    if ($_FILES['profilePicture']['error'] !== UPLOAD_ERR_OK) {
        error_log('Upload error code: ' . $_FILES['profilePicture']['error']);
        error_log('File array: ' . print_r($_FILES, true));
        echo json_encode(['success' => false, 'error' => 'File upload error: code ' . $_FILES['profilePicture']['error']]);
        exit;
    }    

    // Validate the file type
    $fileType = mime_content_type($_FILES['profilePicture']['tmp_name']);
    if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
        exit;
    }

    // Read file contents
    $fileData = file_get_contents($_FILES['profilePicture']['tmp_name']);
    if ($fileData) {
        $stmt = $conn->prepare("UPDATE doctors SET profile_picture = ? WHERE doctor_id = ?");
        $stmt->bind_param("si", $fileData, $doctor_id); // Use "s" for blobs
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update profile picture in the database.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to read file.']);
    }
    exit;
}

// Fetch the current profile picture
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT profile_picture FROM doctors WHERE doctor_id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stmt->bind_result($profilePicture);
    $stmt->fetch();

    if ($profilePicture) {
        header("Content-Type: image/jpeg");
        echo $profilePicture;
    } else {
        header("Content-Type: application/json");
        echo json_encode(['error' => 'No profile picture found.']);
    }
    exit;
}
?>