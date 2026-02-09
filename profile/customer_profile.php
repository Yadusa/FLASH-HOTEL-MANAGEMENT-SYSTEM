<?php
session_start();
include "../db_customer.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT * FROM customer WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to right, #eef2ff, #f4f5f7);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding-top: 50px;
}

/* Profile card */
.profile-box {
    background: #fff;
    border-radius: 20px;
    padding: 40px 30px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    position: relative;
    transition: transform 0.3s, box-shadow 0.3s;
}
.profile-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* Title inside card */
.profile-box h2 {
    text-align: center;
    color: #4f46e5;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 30px;
    font-size: 24px;
}

/* Profile fields */
.profile-box p {
    font-size: 16px;
    color: #374151;
    margin: 12px 0;
    display: flex;
    align-items: center;
}

.profile-box p i {
    color: #4f46e5;
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.profile-box strong {
    color: #1f2937;
    flex: 1;
}

/* Buttons */
.profile-box .btn {
    display: inline-block;
    margin-top: 20px;
    margin-right: 10px;
    padding: 12px 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}
.profile-box .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99,102,241,0.3);
}

/* Responsive */
@media (max-width: 600px) {
    .profile-box {
        padding: 30px 20px;
    }
    .profile-box p {
        font-size: 15px;
    }
}
</style>
</head>
<body>

<div class="profile-box">
    <!-- Title inside card -->
    <a class="back-link" href="../hotel.php">← Back to Hotel</a>
    <h2>Profile Details</h2>

    <p><i class="fas fa-user"></i> <strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><i class="fas fa-id-card"></i> <strong>Full Name:</strong> <?= htmlspecialchars($user['cust_name']); ?></p>
    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($user['cust_email']); ?></p>
    <p><i class="fas fa-phone"></i> <strong>Contact:</strong> <?= htmlspecialchars($user['contact_number']); ?></p>
    <p><i class="fas fa-home"></i> <strong>Address:</strong> <?= htmlspecialchars($user['address']); ?></p>

    <a href="edit_profile.php" class="btn">Edit Profile</a>
    <a href="booking_history.php" class="btn">My Bookings</a>
</div>

</body>
</html>
