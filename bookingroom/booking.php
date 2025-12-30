<?php
session_start();

// Redirect to login if not logged in
if(!isset($_SESSION['customer_username'])) {
    header("Location: ../customer_login.php");
    exit();
}

include '../db.php';

// Get room info
if(!isset($_GET['room_name']) || !isset($_GET['room_price'])) {
    die("Room information not provided.");
}

$room_name = $_GET['room_name'];
$room_price = $_GET['room_price'];

// Booking submission
$success_message = '';
$total_price = 0;
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_username = $_SESSION['customer_username'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = intval($_POST['guests']);

    if(empty($checkin) || empty($checkout) || $guests < 1){
        $error_message = "Please fill in all fields correctly.";
    } else {
        // --- Calculate number of nights and total price ---
        $diff = strtotime($checkout) - strtotime($checkin);
        $nights = max(1, ceil($diff / (60*60*24))); // at least 1 night
        $total_price = $nights * $room_price;

        // --- Insert booking into database ---
        $stmt = $conn->prepare("INSERT INTO bookings (customer_username, room_name, room_price, checkin, checkout, guests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissii", $customer_username, $room_name, $room_price, $checkin, $checkout, $guests, $total_price);

        if($stmt->execute()){
    // $success_message = "Booking successful! Total Price: RM $total_price";
    header("Location: ../payment.php?total_price=$total_price&room_name=" . urlencode($room_name));
    exit();
} else {
    $error_message = "Error while booking: " . $conn->error;
}

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Room | The Obsidian KL</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background-color: #f4f4f8;
    color: #333;
}

.top-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 15px 30px;
    background-color: #1f2933;
    color: #fff;
    font-weight: 500;
}
.top-bar a {
    color: #fff;
    margin-left: 15px;
    text-decoration: none;
}
.top-bar a:hover {
    text-decoration: underline;
}

.booking-container {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
    padding: 30px;
    text-align: center;
}

.booking-container h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #1f2933;
}

.booking-container h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    margin: 10px 0;
    color: #111827;
}

.booking-container p {
    font-size: 1rem;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    text-align: left;
}

form label {
    font-weight: 500;
    margin-bottom: 5px;
}

form input {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
    transition: 0.3s;
}
form input:focus {
    border-color: #1f2933;
    outline: none;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: 0.3s;
    border: none;
}

.btn-primary {
    background-color: #1f2933;
    color: #fff;
}
.btn-primary:hover {
    background-color: #374151;
}

.btn-secondary {
    background-color: #e5e7eb;
    color: #111827;
}
.btn-secondary:hover {
    background-color: #d1d5db;
}

.success-message {
    color: green;
    font-weight: 500;
    margin-bottom: 15px;
}

.error-message {
    color: red;
    font-weight: 500;
    margin-bottom: 15px;
}

@media (max-width: 640px) {
    .booking-container {
        margin: 20px;
        padding: 20px;
    }
    .booking-container h1 {
        font-size: 2rem;
    }
    .booking-container h2 {
        font-size: 1.5rem;
    }
}
</style>
</head>
<body>

<div class="top-bar">
    Welcome, <?php echo htmlspecialchars($_SESSION['customer_username']); ?> |
    <a href="../customer_logout.php">Logout</a>
</div>

<div class="booking-container">
    <h1>Book Your Room</h1>
    <h2><?php echo htmlspecialchars($room_name); ?></h2>
    <p>Price: RM <?php echo htmlspecialchars($room_price); ?> / night</p>

    <?php if(!empty($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php elseif(!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
    <label for="checkin">Check-in Date:</label>
    <input type="date" name="checkin" id="checkin" required>

    <label for="checkout">Check-out Date:</label>
    <input type="date" name="checkout" id="checkout" required>

    <label for="guests">Number of Guests:</label>
    <input type="number" name="guests" id="guests" min="1" value="1" required>

    <?php if($total_price > 0): ?>
        <p style="font-weight:600;">Total Price: RM <?php echo $total_price; ?></p>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Confirm Booking</button>
</form>


    <a href="roombooking.php" class="btn btn-secondary" style="margin-top:20px;">← Back to Rooms</a>
</div>

</body>
</html>
