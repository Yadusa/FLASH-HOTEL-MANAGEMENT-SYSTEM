<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        // Prepare SQL to insert new customer
        $stmt = $conn->prepare(
            "INSERT INTO customer (username, email, phone, password)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters (plain text password)
        $stmt->bind_param("ssss", $username, $email, $phone, $password);

        if ($stmt->execute()) {
            // Redirect to login page with success message
            header("Location: customer_login.php?registered=1");
            exit();
        } else {
            $error = "Username or email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Register | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { margin: 0; height: 100vh; background: linear-gradient(135deg, #1f2933, #111827); display: flex; justify-content: center; align-items: center; }
.register-container { background: #fff; width: 100%; max-width: 420px; padding: 45px 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); text-align: center; animation: fadeIn 0.8s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.register-container h1 { margin-bottom: 6px; font-weight: 600; font-size: 26px; color: #111827; }
.register-container p.subtitle { font-size: 14px; color: #6b7280; margin-bottom: 28px; }
.form-group { margin-bottom: 16px; text-align: left; }
.form-group label { font-size: 13px; color: #374151; margin-bottom: 6px; display: block; font-weight: 500; }
.form-group input { width: 100%; padding: 13px 14px; border-radius: 12px; border: 1px solid #d1d5db; font-size: 14px; transition: 0.25s; }
.form-group input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,0.18); }
.register-btn { width: 100%; padding: 14px; border: none; border-radius: 14px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; font-size: 15px; font-weight: 500; cursor: pointer; margin-top: 12px; transition: 0.3s ease; }
.register-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(99,102,241,0.4); }
.error { background: #fee2e2; color: #b91c1c; padding: 11px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
.login-link { margin-top: 22px; font-size: 14px; color: #4b5563; }
.login-link a { color: #4f46e5; text-decoration: none; font-weight: 500; }
.login-link a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="register-container">
    <form method="POST" action="">
        <?php if(!empty($error)) { echo '<div class="error">' . $error . '</div>'; } ?>
        <h1>Customer Register</h1>
        <p class="subtitle">Register to book your perfect stay</p>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="jason" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="jason@gmail.com" required>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="+60 12-345 6789">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="register-btn">Create Account</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="customer_login.php">Login</a>
    </div>
</div>

</body>
</html>
