<?php
include 'db.php';

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

$stmt = $conn->prepare(
    "SELECT id FROM customer WHERE reset_token=? AND token_expiry > NOW()"
);
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    die("Invalid or expired token.");
}

$stmt->bind_result($id);
$stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $update = $conn->prepare(
        "UPDATE customer SET password_hash=?, reset_token=NULL, token_expiry=NULL WHERE id=?"
    );
    $update->bind_param("si", $newPassword, $id);
    $update->execute();

    echo "<script>alert('Password reset successful!'); window.location='customer_login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
</head>
<body>
<h2>Reset Password</h2>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Reset Password</button>
</form>

</body>
</html>
