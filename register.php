<?php
include 'db_connection.php';

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $specialization = $conn->real_escape_string($_POST['specialization']);

    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $emailCheckQuery = "SELECT * FROM doctors WHERE email = '$email'";
        $emailCheckResult = $conn->query($emailCheckQuery);

        if ($emailCheckResult->num_rows > 0) {
            $errorMessage = "Email is already registered.";
        } else {
            $sql = "INSERT INTO doctors (full_name, email, specialization, password) 
                    VALUES ('$fullName', '$email', '$specialization', '$hashedPassword')";

            if ($conn->query($sql)) {
                header('Location: login.php');
                exit;
            } else {
                $errorMessage = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Registration - MedConnectPro</title>
    <link rel="icon" href="img/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="img/logo_main.png" alt="Logo"></a>
        </div>
    </nav>

    <div class="container form-container">
        <br />
        <h2 class="text-center mb-4">Register Here</h2>

        <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="fullName" class="form-label fw-bold">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="fullName" required />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8" />
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label fw-bold">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required />
            </div>
            <div class="mb-3">
                <label for="specialization" class="form-label fw-bold">Specialization</label>
                <input type="text" class="form-control" id="specialization" name="specialization" />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="termsAgreement" required />
                <label class="form-check-label" for="termsAgreement">I agree to the terms and conditions</label>
            </div>
            <button type="submit" class="btn btn-gradient-purple w-100">Register</button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <p>Already a registered doctor? Log in below:</p>
        <a href="login.php" class="btn btn-gradient-purple">Login</a>
    </div>
    <br><br><br>

    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/register.js"></script>
    < script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js">
        </script>
</body>

</html>