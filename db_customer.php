<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "flashhotel";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Customer DB connection failed: " . $conn->connect_error);
}
?>
