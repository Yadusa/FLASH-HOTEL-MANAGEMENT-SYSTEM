<?php
include 'db.php';

if (isset($_POST['register'])) {
    $customer_id = $_POST['customer_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    // Check if customer id exists
    $check = mysqli_query($conn, "SELECT * FROM customers WHERE customer_id='$customer_id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Customer ID already exists!'); window.location='register.html';</script>";
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO customers (customer_id, password, fullname, email)
            VALUES ('$customer_id', '$password', '$fullname', '$email')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Account created successfully!'); window.location='hotel.html';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
