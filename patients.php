<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db_connection.php';

$doctor_id = $_SESSION['doctor_id'];

$stmt = $conn->prepare("SELECT ehr_id, patient_name, dob, gender, country FROM patient_ehr WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$patients = [];
while ($row = $result->fetch_assoc()) {
    $dob = $row['dob'];
    if ($dob && $dob !== '0000-00-00') {
        $dateParts = explode('-', $dob);
        $dobDisplay = $dateParts[2].'-'.$dateParts[1].'-'.$dateParts[0];
    } else {
        $dobDisplay = '';
    }

    $parts = explode(' ', $row['patient_name'], 2);
    $firstName = $parts[0];
    $lastName = isset($parts[1]) ? $parts[1] : '';

    $patients[] = [
        'ehr_id' => $row['ehr_id'],
        'firstName' => $firstName,
        'lastName' => $lastName,
        'dob' => $dobDisplay,
        'gender' => $row['gender'],
        'country' => $row['country']
    ];
}
$patientsJson = json_encode($patients);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient List - MedConnectPro</title>
    <link rel="icon" href="img/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="doctor-dashboard.php"><img src="img/logo_main.png" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav" id="navbarLinks">
                    <li class="nav-item"><a class="nav-link" href="doctor-dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="ehr.php">New Patient</a></li>
                </ul>
                <a href="index.php" id="loginLogoutButton" class="btn btn-danger ms-auto">Logout</a>
            </div>
        </div>
    </nav>

    <script>
    var patientsData = <?php echo $patientsJson; ?>;
    </script>

    <div class="container mt-4">
        <h2>Patient List</h2>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for patients..."
                oninput="searchPatients()" />
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-secondary sort-btn" onclick="sortPatients('asc')">Sort A-Z</button>
            <button class="btn btn-secondary sort-btn" onclick="sortPatients('desc')">Sort Z-A</button>
        </div>

        <table class="patient-info-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Country of Origin</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="patientList"></tbody>
        </table>

        <a href="ehr.php" class="btn btn-gradient-purple mt-3">Add New Patient</a>
    </div>

    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/patients.js"></script>
</body>

</html>