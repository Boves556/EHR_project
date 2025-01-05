<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit;
}
$doctorName = $_SESSION['doctorName'];
$specialization = $_SESSION['specialization'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Doctor Dashboard</title>
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
                    <li class="nav-item"><a class="nav-link" href="patients.php">Your Patients</a></li>
                    <li class="nav-item"><a class="nav-link" href="ehr.php">New Patient</a></li>
                </ul>
                <a href="index.php" id="loginLogoutButton" class="btn btn-danger ms-auto">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <div class="profile-pic-container">
                <img id="doctorProfilePic" src="img/profile_placeholder.jpg" alt="Doctor's Profile"
                    class="rounded-circle shadow mb-3" style="width: 150px; height: 150px; object-fit: cover" />
                <input type="file" id="uploadProfilePic" class="form-control d-none" accept="image/*" />
                <br>
                <button class="btn btn-gradient-purple"
                    onclick="document.getElementById('uploadProfilePic').click();">Change Profile Picture</button>
            </div>
        </div>
        <h1 class="text-center">Welcome, Dr. <?php echo htmlspecialchars($doctorName); ?></h1>
        <p class="lead text-center">Specialization: <?php echo htmlspecialchars($specialization); ?></p>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow p-3">
                    <h3 class="card-title dashtext">Quick Actions</h3>
                    <ul class="list-group">
                        <li class="list-group-item"><a href="patients.php" class="dashsubtext">View Patient List</a>
                        </li>
                        <li class="list-group-item"><a href="ehr.php" class="dashsubtext">Add New Patient</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow p-3">
                    <h3 class="card-title dashtext">Your Statistics</h3>
                    <p>Total patients: <span id="patientCount">Loading...</span></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/doctor-dashboard.js"></script>
</body>

</html>