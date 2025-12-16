<?php
session_start();

$conn = new mysqli("localhost", "root", "", "flashhotel");
if ($conn->connect_error) {
    die("Database connection failed");
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM customer WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password_hash'])) {

        // store login session
        $_SESSION['customer'] = $user['cust_name'];

        // ALWAYS redirect to main page
        header("Location: ../hotel.html");
        exit;
    }
}

// login failed
echo "<script>alert('Invalid username or password'); window.history.back();</script>";

$conn->close();
?>
