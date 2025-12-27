<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_customer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $phone, $password);

    if ($stmt->execute()) {
        header("Location: customer_login.php?registered=1");
        exit();
    } else {
        $error = "Username or email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Register</title>
<style>
    /* Background */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    /* Card */
    .register-card {
        background: #fff;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .register-card h2 {
        margin-bottom: 20px;
        color: #333;
    }

    /* Input fields */
    .register-card input {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 10px;
        transition: 0.3s;
        font-size: 16px;
    }

    .register-card input:focus {
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102,126,234,0.5);
        outline: none;
    }

    /* Button */
    .register-card button {
        width: 100%;
        padding: 12px;
        background-color: #667eea;
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
    }

    .register-card button:hover {
        background-color: #5a67d8;
    }

    /* Error message */
    .error {
        color: red;
        margin-bottom: 10px;
    }

    /* Login link */
    .register-card p {
        margin-top: 15px;
        font-size: 14px;
    }

    .register-card a {
        color: #667eea;
        text-decoration: none;
    }

    .register-card a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="register-card">
    <h1>Customer Register</h1>
    <p class="subtitle">Register to book your perfect stay</p>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="customer_login.php">Login</a></p>
</div>

</body>
</html>
