<?php
session_start();
require_once "../config/db.php";

$username = $_POST['username'];
$cust_name = $_POST['cust_name'];
$cust_email = $_POST['cust_email'];
$contact = $_POST['contact_numer'];
$dob = $_POST['dob'];
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$room = $_POST['room'];

$sql = "INSERT INTO customer 
(username, cust_name, cust_email, cust_address, contact_numer, dob, password_hash)
VALUES
('$username','$cust_name','$cust_email','$cust_address','$contact','$dob','$password_hash')";

if ($conn->query($sql)) {

    $_SESSION['customer'] = $username;

    if ($room != "") {
        header("Location: booking-form.html?room=" . $room);
    } else {
        header("Location: ../hotel.html");
    }
} else {
    echo "Username or Email already exists!";
}

$conn->close();
?>

