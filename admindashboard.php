<?php
// ... (Your existing session start and admin check) ...
require_once "db_connection.php"; // Ensure you have a DB connection file

// 1. Fetch Total Rooms and Available Rooms
$query = "SELECT SUM(total_quantity) as total, SUM(available_quantity) as available FROM rooms";
$result = mysqli_query($conn, $query);
$roomData = mysqli_fetch_assoc($result);

// 2. Fetch Total Bookings (Occupied)
$bookingQuery = "SELECT COUNT(*) as occupied FROM bookings WHERE status = 'confirmed'";
$bookingResult = mysqli_query($conn, $bookingQuery);
$bookingData = mysqli_fetch_assoc($bookingResult);
?>

<div class="cards">
    <div class="card card-blue">
        <h4> Total Rooms</h4>
        <p><?php echo $roomData['total']; ?> Total Capacity</p>
    </div>

    <div class="card card-green">
        <h4> Rooms Available</h4>
        <p><?php echo $roomData['available']; ?> Available Now</p>
    </div>

    <div class="card card-green">
        <h4> Rooms Occupied</h4>
        <p><?php echo $bookingData['occupied']; ?> Currently Stayed</p>
    </div>
    
    </div>