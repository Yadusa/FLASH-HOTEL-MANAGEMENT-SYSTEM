<?php
$host = "localhost";      // XAMPP default
$user = "root";          // XAMPP default
$pass = "";              // XAMPP default (no password)
$dbname = "flashhotel";    // Change to your actual database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
