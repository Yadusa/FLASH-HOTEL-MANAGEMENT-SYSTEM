<?php
session_start();
if (!isset($_SESSION['customer_username'])) {
    header("Location: ../customer_login.php");
    exit();
}

$room_name  = $_GET['room_name'] ?? '';
$room_price = floatval($_GET['room_price'] ?? 0);
$checkin    = $_GET['check_in'] ?? '';
$checkout   = $_GET['check_out'] ?? '';
$adults     = intval($_GET['adults'] ?? 1);
$children   = intval($_GET['children'] ?? 0);

$nights = 0;
if ($checkin && $checkout) {
    $nights = max(1, ceil((strtotime($checkout) - strtotime($checkin)) / 86400));
}

$total_price = $nights * $room_price;

// Simple image mapper
$imageMap = [
    'Executive Suite'           => 'luxury_suite.jpg',
    'Deluxe King Room'          => 'deluxeroom.jpg',
    'Family Room'               => 'familyroom.jpg',
    'Executive Deluxe King'     => 'executive-deluxe-king.jpg',
    'Standard Double Room'      => 'standarddouble.jpg',
    'Budget Twin Room'          => 'budget.jpg'
];


$room_image = $imageMap[$room_name] ?? 'budget.jpg'; // fallback image

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Confirm Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
.bg-blur {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    filter: blur(14px);
    transform: scale(1.15);
    z-index: -1;
}

.container {
    max-width: 480px;
    margin: 80px auto;
    padding: 30px;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border-radius: 18px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.35);
}


body {
    font-family: 'Poppins', sans-serif;
    background: #f3f4f6;
    margin: 0;
    padding: 40px 15px;
}
.container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.1);
    display: grid;
    grid-template-columns: 1fr 1fr;
}
.image-box img {
    width: 100%;
    height: 260px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 20px;
}
.content {
    padding: 35px;
}
.content h2 {
    margin-top: 0;
    font-size: 2rem;
}
.detail {
    margin: 10px 0;
    font-size: 1rem;
}
.total {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 20px;
}
.actions {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}
.actions button {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: .3s;
}
.confirm-btn {
    background: #1f2933;
    color: #fff;
}
.confirm-btn:hover { background:#374151; }

.back-btn {
    background: #e5e7eb;
}
.back-btn:hover { background:#d1d5db; }

@media(max-width: 768px) {
    .container { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="bg-blur" style="
    background: url('<?= htmlspecialchars($room_image) ?>') center / cover no-repeat;
    filter: blur(14px);
    transform: scale(1.15);
    z-index: -1;
"></div>

<div class="container">
    <div class="image-box">
    <img src="<?= htmlspecialchars($room_image) ?>" alt="<?= htmlspecialchars($room_name) ?>">
</div>


    <div class="content">
        <h2>Confirm Your Booking</h2>

        <p class="detail"><strong>Room:</strong> <?= htmlspecialchars($room_name) ?></p>
        <p class="detail"><strong>Dates:</strong> <?= htmlspecialchars($checkin) ?> → <?= htmlspecialchars($checkout) ?> (<?= $nights ?> nights)</p>
        <p class="detail"><strong>Guests:</strong> <?= $adults ?> Adults, <?= $children ?> Children</p>
        <p class="detail"><strong>Price:</strong> RM <?= number_format($room_price,2) ?> × <?= $nights ?> nights</p>

        <p class="total">Total: RM <?= number_format($total_price,2) ?></p>

        <form action="../payment.php" method="GET" class="actions">
            <input type="hidden" name="room_name" value="<?= htmlspecialchars($room_name) ?>">
            <input type="hidden" name="room_price" value="<?= $room_price ?>">
            <input type="hidden" name="check_in" value="<?= $checkin ?>">
            <input type="hidden" name="check_out" value="<?= $checkout ?>">
            <input type="hidden" name="adults" value="<?= $adults ?>">
            <input type="hidden" name="children" value="<?= $children ?>">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">

            <button type="submit" class="confirm-btn">Confirm Booking</button>
            <button type="button" class="back-btn" onclick="history.back()">Back</button>
        </form>
    </div>
</div>

</body>
</html>
