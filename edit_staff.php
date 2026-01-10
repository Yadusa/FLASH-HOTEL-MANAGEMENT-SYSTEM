<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] != "superadmin") {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET["id"])) {
    header("Location: manage_staff.php");
    exit;
}

$id = $_GET["id"];
// Use prepared statements for security
$stmt = $conn->prepare("SELECT * FROM staffs WHERE staff_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    header("Location: manage_staff.php");
    exit;
}

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
                staff_name=?, 
                staff_dob=?, 
                staff_salary=?, 
                staff_join_date=?, 
                staff_position=?, 
                staff_email=?, 
                staff_phone=?, 
                staff_status=? 
               WHERE staff_id=?";
    
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssdssssss", $name, $dob, $salary, $join, $position, $email, $phone, $status, $id);

    if ($stmt->execute()) {
        header("Location: manage_staff.php?msg=updated");
        exit;
    } else {
        $error = "Error updating staff: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff | FLASH Hotel</title>
    <link rel="stylesheet" href="subadmin.css">
    <style>
        /* Custom tweak for the select dropdown to match input styling */
        .form-select {
            width: 100%;
            padding: 14px;
            border: 1px solid #dcdde1;
            border-radius: 8px;
            font-size: 1rem;
            background-color: #f9f9f9;
            cursor: pointer;
        }
        /* Ensure the form rows handle spacing correctly */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 5px;
        }
        .form-row .form-group {
            flex: 1;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2>FLASH Hotel Admin</h2>
        <p class="role">Super Admin</p>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_staff.php" class="active">Manage Staff</a>
    <a href="add_staff.php">Add new Staff</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Edit Staff Details</h2>
            <p>Modify information for <strong><?php echo htmlspecialchars($row['staff_name']); ?></strong> (ID: <?php echo $id; ?>)</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="staff_name" value="<?= htmlspecialchars($row['staff_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="staff_position" value="<?= htmlspecialchars($row['staff_position']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="staff_dob" value="<?= $row['staff_dob'] ?>" required max="2006-12-31">
                </div>
                <div class="form-group">
                    <label>Monthly Salary</label>
                    <input type="number" step="0.01" name="staff_salary" value="<?= $row['staff_salary'] ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Join Date</label>
                    <input type="date" name="staff_join_date" value="<?= $row['staff_join_date'] ?>" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Employment Status</label>
                    <select name="staff_status" class="form-select">
                        <option value="Active" <?= $row['staff_status']=='Active'?'selected':'' ?>>Active</option>
                        <option value="Inactive" <?= $row['staff_status']=='Inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="staff_email" value="<?= htmlspecialchars($row['staff_email']) ?>">
                </div>
                <div class="form-group">
                 <label>Phone Number</label>
                 <input 
                  type="text" 
                  name="staff_phone" 
                  placeholder="e.g. 0123456789" 
                  required
                  pattern="\d{10}" 
                  title="Please enter exactly 10 digits" 
                  maxlength="10"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                  value="<?= isset($row['staff_phone']) ? htmlspecialchars($row['staff_phone']) : '' ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit" class="btn">Update Staff Member</button>
                <a href="manage_staff.php" class="btn-link">Cancel and Go Back</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>