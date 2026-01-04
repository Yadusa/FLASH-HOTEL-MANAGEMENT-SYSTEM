<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="customer.css">
</head>
<body>

<div class="auth-container">
    <h2>Create Account</h2>

    <form action="customer_register_process.php" method="POST">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Full Name</label>
        <input type="text" name="cust_name" required>

        <label>Email</label>
        <input type="email" name="cust_email" required>

        <label>Contact Number</label>
        <input type="text" name="contact_number" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Register</button>

        <p class="switch">
            Already have an account?
            <a href="customer_login.php">Login here</a>
        </p>
    </form>
</div>

</body>
</html>
