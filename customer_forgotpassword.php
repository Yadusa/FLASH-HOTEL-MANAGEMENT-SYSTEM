<?php
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM customer WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $conn->prepare(
                "UPDATE customer SET password_hash = ? WHERE username = ?"
            );
            $update->bind_param("ss", $hashedPassword, $username);
            $update->execute();

            $success = "Password reset successful. You may now log in.";
        } else {
            $error = "Username not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | The Obsidian</title>

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

.reset-card {
    background: #fff;
    width: 100%;
    max-width: 420px;
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

.reset-card h2 {
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 26px;
    color: #111827;
}

.reset-card .subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 28px;
}

.reset-card input {
    width: 100%;
    padding: 13px 14px;
    margin-bottom: 16px;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: 0.25s;
}

.reset-card input:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.18);
}

.reset-card button {
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
}

.reset-card button:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(99,102,241,0.4);
}

.error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 11px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 18px;
}

.success {
    background: #dcfce7;
    color: #166534;
    padding: 11px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 18px;
}

.reset-card p {
    margin-top: 18px;
    font-size: 14px;
    color: #4b5563;
}

.reset-card a {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 500;
}

.reset-card a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="reset-card">
    <h2>Forgot Password</h2>
    <p class="subtitle">Reset your account password</p>

    <?php
    if ($error) echo "<div class='error'>$error</div>";
    if ($success) echo "<div class='success'>$success</div>";
    ?>

    <form method="POST" novalidate>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>

    <p><a href="customer_login.php">Back to Login</a></p>
</div>

</body>
</html>
