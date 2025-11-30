<?php
include 'db.php';

$id = $_POST['id'];
$name = $_POST['guest_name'];
$room = $_POST['room_type'];
$checkin = $_POST['check_in'];
$checkout = $_POST['check_out'];
$price = $_POST['price'];

if ($id == "") {
    // Insert new booking
    $sql = "INSERT INTO bookings (guest_name, room_type, check_in, check_out, price)
            VALUES ('$name', '$room', '$checkin', '$checkout', '$price')";
} else {
    // Update existing
    $sql = "UPDATE bookings SET 
            guest_name='$name',
            room_type='$room',
            check_in='$checkin',
            check_out='$checkout',
            price='$price'
            WHERE id=$id";
}

$conn->query($sql);

echo "success";
?>
