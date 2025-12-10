<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

if (isset($_POST['submit'])) {
    $id = $_POST["staff_id"];
    $name = $_POST["staff_name"];
    $dob = $_POST["staff_dob"];
    $salary = $_POST["staff_salary"];
    $join = $_POST["staff_join_date"];
    $position = $_POST["staff_position"];
    $email = $_POST["staff_email"];
    $phone = $_POST["staff_phone"];
    $status = $_POST["staff_status"];

    $sql = "INSERT INTO staffs (staff_id, staff_name, staff_dob, staff_salary, staff_join_date, staff_position, staff_email, staff_phone, staff_status)
            VALUES ('$id', '$name', '$dob', '$salary', '$join', '$position', '$email', '$phone', '$status')";

    if ($conn->query($sql)) {
        header("Location: manage_staff.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="sidebar">
    <h2>FLASH Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="add_staff.php" class="active">Add new Staff</a>
    <a href="logout.php">Logout</a>
</div>

<div class="form-container">
    <h2>Add New Staff</h2>

    <form method="POST">

        <label>Staff ID:</label>
        <input type="text" name="staff_id" required>

        <label>Full Name:</label>
        <input type="text" name="staff_name" required>

        <label>Date of Birth:</label>
        <input type="date" name="staff_dob" required>

        <label>Salary:</label>
        <input type="number" step="0.01" name="staff_salary" required>

        <label>Join Date:</label>
        <input type="date" name="staff_join_date" required>

        <label>Position:</label>
        <input type="text" name="staff_position" required>

        <label>Email:</label>
        <input type="email" name="staff_email">

        <label>Phone:</label>
        <input type="text" name="staff_phone">

        <label>Status:</label>
        <select name="staff_status">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>

        <button type="submit" name="submit" class="btn">Add Staff</button>

    </form>
</div>

</body>
</html>
