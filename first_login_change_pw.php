<?php
session_start();
require "db.php";

// Redirect if not logged in or if they've already changed their password
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pw = $_POST['new_password'];
    $confirm_pw = $_POST['confirm_password'];

    // PHP Regex for validation:
    // ^(?=.*[a-z]) : at least one lowercase
    // (?=.*[A-Z])  : at least one uppercase
    // (?=.*\d)     : at least one number
    // (?=.*[\W_])  : at least one symbol/special char
    // .{8,}        : at least 8 characters
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (!preg_match($pattern, $new_pw)) {
        $error = "Password does not meet the security requirements.";
    } elseif ($new_pw !== $confirm_pw) {
        $error = "Passwords do not match.";
    } else {
        // Validation passed: Hash and update
        $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET password = ?, is_first_login = 0 WHERE id = ?");
        $stmt->bind_param("si", $hashed, $_SESSION['admin_id']);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=welcome");
            exit;
        } else {
            $error = "Database error. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Your Account | FLASH Hotel</title>
    <link rel="stylesheet" href="subadmin.css">
    <style>
        .requirement-list { margin-top: 15px; padding: 0; list-style: none; font-size: 0.85rem; }
        .requirement-list li { margin-bottom: 5px; color: #d9534f; }
        .requirement-list li.met { color: #5cb85c; }
        .error-box { background: #ffebe6; color: #bf2600; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ffbdad; }
    </style>
</head>
<body style="background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

<div class="form-container" style="width: 400px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <div class="form-header">
        <h2 style="margin-top: 0;">Set New Password</h2>
        <p style="color: #666;">This is your first login. For security, please update your temporary password.</p>
    </div>

    <?php if($error): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="form-group" style="margin-bottom: 15px;">
            <label>New Password</label>
            <input type="password" name="new_password" id="new_password" required placeholder="Enter new password" style="width: 100%; padding: 10px; margin-top: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required placeholder="Confirm new password" style="width: 100%; padding: 10px; margin-top: 5px;">
        </div>

        <div class="password-policy" style="background: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
            <strong>Password Requirements:</strong>
            <ul class="requirement-list">
                <li>● Minimum 8 characters</li>
                <li>● At least one uppercase letter (A-Z)</li>
                <li>● At least one lowercase letter (a-z)</li>
                <li>● At least one numeric digit (0-9)</li>
                <li>● At least one special character (!@#$%^&*)</li>
            </ul>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary" style="width: 100%; background: #1f2933; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer;">
                Update Password & Continue
            </button>
        </div>
    </form>
</div>

</body>
</html>