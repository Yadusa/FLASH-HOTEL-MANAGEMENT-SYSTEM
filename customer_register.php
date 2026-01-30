<?php
session_start();
require_once "db.php";

$message = ""; // To handle errors or success messages

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username        = trim($_POST['username']);
    $cust_name       = trim($_POST['cust_name']);
    $cust_email      = trim($_POST['cust_email']);
    $contact_number  = trim($_POST['contact_number']);
    $address = trim($_POST['address']);
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // 1. Basic validation
    if (empty($username) || empty($cust_name) || empty($cust_email) || empty($contact_number) || empty($address) || empty($password)) {

        $message = "error_empty";
    } elseif ($password !== $confirmPassword) {
        $message = "error_mismatch";
    } else {
        // 2. Check if exists
        $checkStmt = $conn->prepare("SELECT id FROM customer WHERE username = ? OR cust_email = ?");
        $checkStmt->bind_param("ss", $username, $cust_email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $message = "error_exists";
        } else {
            // 3. Hash and Insert
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
    "INSERT INTO customer 
    (username, cust_name, cust_email, contact_number, address, password_hash, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, NOW())"
);
            $stmt->bind_param("ssssss", 
    $username, 
    $cust_name, 
    $cust_email, 
    $contact_number, 
    $address,
    $hashedPassword
);


            if ($stmt->execute()) {
                // Success! Redirect with a success flag
                header("Location: customer_register.php?success=1");
                exit;
            } else {
                $message = "error_failed";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration | The Obsidian</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Keep your existing CSS here */
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { margin: 0; height: 100vh; background: linear-gradient(135deg, #111827, #1f2933); display: flex; justify-content: center; align-items: center; }
        .register-card { background: #fff; width: 100%; max-width: 460px; padding: 45px 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); text-align: center; }
        .register-card input { width: 100%; padding: 13px 14px; margin-bottom: 16px; border-radius: 12px; border: 1px solid #d1d5db; }
        .register-card button { width: 100%; padding: 14px; border: none; border-radius: 14px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; cursor: pointer; }
        .alert-error { color: #b91c1c; background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="register-card">
    <h2>Create Account</h2>
    <p class="subtitle">Join us to start your booking</p>

    <?php if(!empty($message)): ?>
        <div class="alert-error"><?php echo str_replace('error_', '', $message); ?></div>
    <?php endif; ?>

    <form action="customer_register.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="cust_name" placeholder="Full Name" required>
        <input type="email" name="cust_email" placeholder="Email Address" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <textarea name="address" placeholder="Address" required style="width: 100%; padding: 13px 14px; margin-bottom: 16px; border-radius: 12px; border: 1px solid #d1d5db;"></textarea>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="customer_login.php">Login here</a></p>
</div>

<script>
    // Check if the URL has success=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        alert("Registration Successful! You can now log in.");
        window.location.href = "customer_login.php?registered=1";
    }
</script>

</body>
</html>
