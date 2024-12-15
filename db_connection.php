<?php
// Database configuration
$servername = "localhost"; // Default XAMPP server
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "medconnectpro"; // Your database name

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment this line to verify the connection (remove after testing)
// echo "Connected successfully";
?>