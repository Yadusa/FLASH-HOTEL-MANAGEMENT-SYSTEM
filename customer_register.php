<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Registration | The Obsidian</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

body {
    margin: 0;
    height: 100vh;
    background: linear-gradient(135deg, #111827, #1f2933);
    display: flex;
    justify-content: center;
    align-items: center;
}

.register-card {
    background: #fff;
    width: 100%;
    max-width: 460px;
    padding: 45px 40px;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    text-align: center;
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.register-card h2 {
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 26px;
    color: #111827;
}

.register-card .subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 28px;
}

.register-card input {
    width: 100%;
    padding: 13px 14px;
    margin-bottom: 16px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: 0.25s;
}

.register-card input:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.18);
}

.register-card button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s ease;
    margin-top: 10px;
}

.register-card button:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(99,102,241,0.4);
}

.register-card p {
    margin-top: 18px;
    font-size: 14px;
    color: #4b5563;
}

.register-card a {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 500;
}

.register-card a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="register-card">
    <h2>Create Account</h2>
    <p class="subtitle">Join us to start your booking</p>

    <form action="customer_register_process.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="cust_name" placeholder="Full Name" required>
        <input type="email" name="cust_email" placeholder="Email Address" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
    </form>

    <p>
        Already have an account?
        <a href="customer_login.php">Login here</a>
    </p>
</div>

</body>
</html>
