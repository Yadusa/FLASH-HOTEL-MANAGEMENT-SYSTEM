<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Change Password | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f5f7;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.card {
    background: #fff;
    padding: 35px 30px;
    border-radius: 14px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.card h2 {
    margin-bottom: 20px;
    text-align: center;
}

label {
    font-weight: 500;
    display: block;
    margin-top: 15px;
}

input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    margin-top: 6px;
}

button {
    width: 100%;
    margin-top: 25px;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #4f46e5;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

button:hover {
    background: #6366f1;
}

.error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.success {
    background: #dcfce7;
    color: #166534;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<div class="card">
    <h2>Change Password</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">Password updated successfully</div>
    <?php endif; ?>

    <form action="update_password.php" method="POST">
        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    </form>

    <!-- Back to Profile link styled like bookings page -->
    <a class="back-link" href="edit_profile.php">← Back to Profile</a>
</div>

<style>
.back-link {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #4f46e5;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}
</style>



</body>
</html>
