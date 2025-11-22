<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$guest_name = $data['guest_name'];
$room_type = $data['room_type'];
$check_in = $data['check_in'];
$check_out = $data['check_out'];
$price = $data['price'];

$sql = "UPDATE bookings SET guest_name='$guest_name', room_type='$room_type', 
        check_in='$check_in', check_out='$check_out', price='$price' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Booking updated successfully"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>