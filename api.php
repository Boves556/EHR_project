<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'db_connection.php';

$doctor_id = $_SESSION['doctor_id'];

// Fetch total patient count
$stmt = $conn->prepare("SELECT COUNT(*) AS patient_count FROM patient_ehr WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$patientCount = $row['patient_count'] ?? 0;

header("Content-Type: application/json");
echo json_encode(['patientCount' => $patientCount]);