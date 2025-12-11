<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $customer_id = $_POST['customer_id'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM customers WHERE customer_id='$customer_id'");

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['customer_id'] = $customer_id;
            echo "<script>alert('Login successful!'); window.location='hotel.html';</script>";
        } else {
            echo "<script>alert('Wrong password!'); window.location='login.html';</script>";
        }

    } else {
        echo "<script>alert('Customer ID not found!'); window.location='login.html';</script>";
    }
}
?>
