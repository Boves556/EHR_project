<?php
// Include database connection
include 'db_connection.php';

session_start();
$errorMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Query to check user credentials
    $sql = "SELECT * FROM doctors WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['loggedIn'] = true;
            $_SESSION['doctor_id'] = $user['id']; // Ensure 'id' is the PK column in doctors table
            $_SESSION['doctorName'] = $user['full_name'];
            $_SESSION['doctorEmail'] = $user['email'];
            $_SESSION['specialization'] = $user['specialization'];

            header('Location: doctor-dashboard.php');
            exit;
        } else {
            $errorMessage = "Invalid email or password.";
        }
    } else {
        $errorMessage = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - MedConnectPro</title>
    <link rel="icon" href="img/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/styles.css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="img/logo_main.png" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container form-container">
        <br>
        <h2 class="text-center mb-4">Log in</h2>
        <?php if ($errorMessage): ?>
        <div class="alert alert-danger text-center">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email address</label>
                <input type="email" name="email" class="form-control" id="email" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" id="password" required />
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="showPassword" />
                <label for="showPassword" class="form-check-label">Show Password</label>
            </div>
            <button type="submit" class="btn btn-gradient-purple w-100">Login</button>
        </form>
        <a href="forgot-password.php" class="d-block text-center mt-2">Forgot password?</a>

        <div class="mt-4 text-center">
            <p>Register as a doctor using the link below:</p>
            <a href="register.php" class="btn btn-gradient-purple">Doctor Registration</a>
        </div>
    </div>

    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/login.js"></script>
</body>

</html>