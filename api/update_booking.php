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

$id = intval($data['id'] ?? 0);
$guest_name = $data['guest_name'] ?? '';
$room_type = $data['room_type'] ?? '';
$check_in = $data['check_in'] ?? '';
$check_out = $data['check_out'] ?? '';
$price = floatval($data['price'] ?? 0);

if ($id > 0 && $guest_name && $room_type && $check_in && $check_out && $price > 0) {
    $stmt = $conn->prepare("UPDATE bookings SET guest_name=?, room_type=?, check_in=?, check_out=?, price=? WHERE id=?");
    $stmt->bind_param("ssssdi", $guest_name, $room_type, $check_in, $check_out, $price, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Booking updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update booking"]);
    }
} else {
    echo json_encode(["error" => "Invalid booking ID or data"]);
}
?>