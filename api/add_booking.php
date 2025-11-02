<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db.php'; 

$data = json_decode(file_get_contents("php://input"), true);

$guest_name = $data['guest_name'] ?? '';
$room_type = $data['room_type'] ?? '';
$check_in = $data['check_in'] ?? '';
$check_out = $data['check_out'] ?? '';
$price = floatval($data['price'] ?? 0);

if ($guest_name && $room_type && $check_in && $check_out && $price > 0) {
    $stmt = $conn->prepare("INSERT INTO bookings (guest_name, room_type, check_in, check_out, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $guest_name, $room_type, $check_in, $check_out, $price);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Booking added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to add booking"]);
    }
} else {
    echo json_encode(["error" => "Invalid input data"]);
}
?>