<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - MedConnectPro</title>
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
                </ul>
                <a href="index.php" id="loginLogoutButton" class="btn btn-theme-secondary ms-auto">Login</a>
            </div>
        </div>
    </nav>

    <div class="container form-container">
        <br>
        <h2 class="text-center mb-4">Reset Your Password</h2>
        <form id="resetPasswordForm">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email address</label>
                <input type="email" class="form-control" id="email" required />
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label fw-bold">New Password</label>
                <input type="password" class="form-control" id="newPassword" required />
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label fw-bold">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" required />
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>
            <button type="submit" class="btn btn-gradient-purple w-100">
                Confirm Password
            </button>
        </form>
    </div>

    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/forgot-password.js"></script>
</body>

</html>