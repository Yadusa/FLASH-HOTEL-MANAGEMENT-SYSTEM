<?php
include 'db.php';

$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

header('Content-Type: application/json');
echo json_encode($bookings);
?>