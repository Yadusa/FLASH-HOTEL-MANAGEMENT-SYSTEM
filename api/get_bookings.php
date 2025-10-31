<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = ""; // your MySQL password if any
$dbname = "flashhotel";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$sql = "SELECT * FROM bookings ORDER BY id DESC";
$result = $conn->query($sql);

$bookings = [];
while($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode($bookings);
$conn->close();
?>
