<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: manage_subadmins.php");
    exit;
}

$id = $_GET["id"];
$stmt = $conn->prepare("SELECT * FROM admins WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Subadmin not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subadmin | FLASH Hotel</title>
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>

<div class="sidebar">
    <h2>FLASH Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Edit Subadmin</h2>
            <p>Modifying details for: <strong><?php echo htmlspecialchars($data['username']); ?></strong></p>
        </div>

        <form action="update_subadmins_process.php" method="POST" class="admin-form">
            <input type="hidden" name="old_id" value="<?php echo $data['id']; ?>">

            <div class="form-group">
                <label for="id">Admin ID</label>
                <input type="number" id="id" name="id" value="<?php echo $data['id']; ?>" required>
                <small>Changing this will update the staff's unique login ID.</small>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                <small>Only enter a value if you wish to reset the password.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Subadmin</button>
                <a href="manage_subadmins.php" class="btn-link">Cancel and Go Back</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>