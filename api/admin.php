<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Database connection (optional for demo)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "flashhotel";
$conn = @new mysqli($host, $user, $pass, $dbname);

if ($conn && $conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$username = $data["username"] ?? "";
$password = $data["password"] ?? "";

// Demo credentials
$correctUsername = "admin";
$correctPassword = "Admin@123";

if ($username === $correctUsername && $password === $correctPassword) {
    echo json_encode(["success" => true, "username" => $username]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid username or password"]);
}

if ($conn) { $conn->close(); }
?>
