<?php
session_start();
include "../db_customer.php";

// Redirect to login if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch user data
$sql = "SELECT * FROM customer WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* Body and background */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f5f7;
    margin: 0;
    padding: 0;
}

/* Center container */
.profile-container {
    max-width: 900px;
    margin: 50px auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

/* Sidebar */
.profile-sidebar {
    flex: 1;
    min-width: 220px;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.profile-sidebar h3 {
    font-weight: 600;
    margin-bottom: 20px;
    color: #111827;
}

.profile-sidebar a {
    display: block;
    padding: 12px 10px;
    margin-bottom: 10px;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.2s;
}

.profile-sidebar a:hover {
    background-color: #e0e7ff;
    color: #4f46e5;
}

/* Main profile form card */
.profile-main {
    flex: 2;
    min-width: 300px;
    background: #fff;
    border-radius: 12px;
    padding: 30px 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    position: relative;
}

/* Back button at top-left */
.back-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    padding: 10px 16px;
    background-color: #e5e7eb;
    color: #111827;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.3s;
}

.back-btn:hover {
    background-color: #d1d5db;
}

/* Headings */
.profile-main h2 {
    margin-top: 60px; /* leave space for back button */
    margin-bottom: 25px;
    color: #111827;
}

/* Labels and inputs */
.profile-main label {
    font-weight: 500;
    color: #374151;
    display: block;
    margin-bottom: 5px;
    margin-top: 15px;
}

.profile-main input,
.profile-main textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.3s;
}

.profile-main input:focus,
.profile-main textarea:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
    outline: none;
}

.profile-main textarea {
    min-height: 80px;
    resize: vertical;
}

/* Save button */
.profile-main button {
    margin-top: 25px;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background-color: #4f46e5;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

.profile-main button:hover {
    background-color: #6366f1;
}

/* Success message */
.success-msg {
    background: #dcfce7;
    color: #166534;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 15px;
    text-align: center;
}

/* Responsive */
@media (max-width: 800px) {
    .profile-container {
        flex-direction: column;
        align-items: center;
    }
    .profile-main {
        width: 90%;
    }
}

/* Read-only (locked) field */
.readonly-field {
    background-color: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}

/* Hint text */
.hint-text {
    font-size: 12px;
    color: #6b7280;
    display: block;
    margin-top: -10px;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="profile-container">
    <!-- Sidebar menu -->
    <div class="profile-sidebar">
        <h3>Hello, <?= htmlspecialchars($user['cust_name']); ?></h3>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="booking_history.php">My Bookings</a>
        <a href="change_password.php">Change Password</a>
        <a href="../customer_logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
    </div>

    <!-- Main profile form -->
    <div class="profile-main">
        <!-- Back to Hotel button -->
        <a href="../hotel.php" class="back-btn">← Back to Hotel</a>

        <h2>Edit Profile</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-msg">Profile updated successfully!</div>
        <?php endif; ?>

        <form action="update_profile.php" method="POST">
            <label>Username</label>
            <input type="text"
            value="<?= htmlspecialchars($user['username']); ?>"
            readonly
            class="readonly-field">
            <small class="hint-text"><br>Username cannot be changed</small>

            <label>Full Name</label>
            <input type="text" name="cust_name" value="<?= htmlspecialchars($user['cust_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="cust_email" value="<?= htmlspecialchars($user['cust_email']); ?>" required>

            <label>Contact Number</label>
            <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number']); ?>" required>

            <label>Address</label>
            <textarea name="address" required><?= htmlspecialchars($user['address']); ?></textarea>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>
