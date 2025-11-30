<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "flashhotel";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"] ?? "";
$password = $data["password"] ?? "";

// Hardcoded admin (since your JS uses a demo admin)
$correctUsername = "admin";
$correctPassword = "Admin@123";

// Validate
if ($username === $correctUsername && $password === $correctPassword) {
    echo json_encode(["success" => true, "username" => $username]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid username or password"]);
}

$conn->close();
?>
