<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = ""; // your MySQL password if any
$dbname = "flashhotel";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit();
}

$name = $conn->real_escape_string($data["name"]);
$room = $conn->real_escape_string($data["room"]);
$checkin = $conn->real_escape_string($data["checkin"]);
$checkout = $conn->real_escape_string($data["checkout"]);
$price = floatval($data["price"]);

$sql = "INSERT INTO bookings (guest_name, room_type, check_in, check_out, price)
        VALUES ('$name', '$room', '$checkin', '$checkout', '$price')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Booking added successfully"]);
} else {
    echo json_encode(["error" => $conn->error]);
}

$conn->close();
?>
