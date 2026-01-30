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
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f5f7;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding-top: 50px;
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #111827;
}

/* Profile card */
.profile-box {
    background: #fff;
    border-radius: 15px;
    padding: 30px 40px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Profile fields */
.profile-box p {
    font-size: 16px;
    color: #374151;
    margin: 12px 0;
}

.profile-box strong {
    color: #111827;
}

/* Links */
.profile-box a {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 16px;
    border-radius: 8px;
    background-color: #4f46e5;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}

.profile-box a:hover {
    background-color: #6366f1;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(99,102,241,0.3);
}

/* Responsive */
@media (max-width: 600px) {
    .profile-box {
        padding: 25px 20px;
    }
}
</style>
</head>
<body>

<h2>My Profile</h2>

<div class="profile-box">
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><strong>Full Name:</strong> <?= htmlspecialchars($user['cust_name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['cust_email']); ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact_number']); ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']); ?></p>

    <a href="edit_profile.php">Edit Profile</a>
    <a href="booking_history.php">My Bookings</a>
</div>

</body>
</html>
