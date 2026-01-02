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
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>
<div class="sidebar">
    <h2>FLASH Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="add_staff.php" class="active">Add new Staff</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Add New Staff</h2>
            <p>Register a new hotel employee into the system.</p>
        </div>

        <form method="POST" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" placeholder="e.g., STF001" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="staff_name" placeholder="John Doe" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="staff_dob" required max="2006-12-31">
                </div>
                <div class="form-group">
                    <label>Salary (Monthly)</label>
                    <input type="number" step="0.01" name="staff_salary" placeholder="0.00" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Join Date</label>
                    <input type="date" name="staff_join_date" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="staff_position" placeholder="e.g., Receptionist" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="staff_email" placeholder="email@hotel.com">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="staff_phone" placeholder="+60...">
                </div>
            </div>

            <div class="form-group">
                <label>Employment Status</label>
                <select name="staff_status" class="form-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit" class="btn">Add Staff Member</button>
                <a href="manage_staff.php" class="btn-link">Cancel and Go Back</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
