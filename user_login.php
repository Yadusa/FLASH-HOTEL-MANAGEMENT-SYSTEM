<?php
session_start();
require_once "db.php";

$username = $_POST['username'];
$password = $_POST['password'];
$room = $_POST['room'];

$sql = "SELECT * FROM customer WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password_hash'])) {

        $_SESSION['customer'] = $user['cust_name'];

        if ($room != "") {
            header("Location: booking-form.html?room=" . $room);
        } else {
            header("Location: hotel.html");
        }
        exit;
    }
}

echo "Invalid username or password";
$conn->close();
?>
