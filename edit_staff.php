<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];
$sql = "SELECT * FROM staffs WHERE staff_id='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (isset($_POST["submit"])) {
    $name = $_POST["staff_name"];
    $dob = $_POST["staff_dob"];
    $salary = $_POST["staff_salary"];
    $join = $_POST["staff_join_date"];
    $position = $_POST["staff_position"];
    $email = $_POST["staff_email"];
    $phone = $_POST["staff_phone"];
    $status = $_POST["staff_status"];

    $update = "UPDATE staffs SET
                staff_name='$name',
                staff_dob='$dob',
                staff_salary='$salary',
                staff_join_date='$join',
                staff_position='$position',
                staff_email='$email',
                staff_phone='$phone',
                staff_status='$status'
               WHERE staff_id='$id'";

    if ($conn->query($update)) {
        header("Location: manage_staff.php");
        exit;
    } else {
        echo "Error updating staff: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <link rel="stylesheet" href="customer.css">
</head>
<body>

<div class="main-content">
    <h2>Edit Staff Details</h2>


    <form method="POST">

        <label>Full Name:</label>
        <input type="text" name="staff_name" value="<?= $row['staff_name'] ?>" required>

        <label>Date of Birth:</label>
        <input type="date" name="staff_dob" value="<?= $row['staff_dob'] ?>" required>

        <label>Salary:</label>
        <input type="number" step="0.01" name="staff_salary" value="<?= $row['staff_salary'] ?>" required>

        <label>Join Date:</label>
        <input type="date" name="staff_join_date" value="<?= $row['staff_join_date'] ?>" required>

        <label>Position:</label>
        <input type="text" name="staff_position" value="<?= $row['staff_position'] ?>" required>

        <label>Email:</label>
        <input type="email" name="staff_email" value="<?= $row['staff_email'] ?>">

        <label>Phone:</label>
        <input type="text" name="staff_phone" value="<?= $row['staff_phone'] ?>">

        <label>Status:</label>
        <select name="staff_status">
            <option value="Active" <?= $row['staff_status']=='Active'?'selected':'' ?>>Active</option>
            <option value="Inactive" <?= $row['staff_status']=='Inactive'?'selected':'' ?>>Inactive</option>
        </select>

        <button type="submit" name="submit" class="btn">Update Staff</button>
    </form>
</div>

</body>
</html>
