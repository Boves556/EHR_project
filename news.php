<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New - MedConnectPro</title>
    <link rel="icon" href="img/logo.png" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/styles.css" />
  </head>

  <body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-fluid">
        <!-- Brand Logo -->
        <div>
          <a class="navbar-brand" href="index.php"
            ><img src="img/logo_main.png" alt="Logo"
          /></a>
        </div>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <!-- Left-aligned links closer to the brand logo -->
          <div class="mx-auto">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="about.php">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="services.php">Services</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="news.php">News</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact</a>
              </li>
            </ul>
          </div>

          <!-- Right-aligned Login and Register buttons -->
          <div>
            <ul class="navbar-nav ms-auto gap-1">
              <li class="nav-item">
                <a class="btn btn-theme-secondary" href="login.php">Login</a>
              </li>
              <li class="nav-item">
                <a class="btn btn-theme-primary" href="register.php"
                  >Register</a
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <div class="container">
      <!-- news section -->
    </div>

    <!-- Footer -->
    <footer class="footbar text-white text-center py-3">
      <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
  </body>
</html>
