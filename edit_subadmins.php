<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

// Check if ID is provided
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
<html>
<head>
    <title>Edit Subadmin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>Hotel Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

    <h2>Edit Subadmin: <?php echo htmlspecialchars($data['username']); ?></h2>

    <form action="update_subadmins_process.php" method="POST" class="form-box">

        <input type="hidden" name="old_id" value="<?php echo $data['id']; ?>">

        <label>ID</label>
        <input type="number" name="id" value="<?php echo $data['id']; ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>

        <label>New Password (Leave blank to keep current)</label>
        <input type="password" name="password" placeholder="Enter new password only if changing">

        <button type="submit" class="btn-save">Update Subadmin</button>
        <a href="manage_subadmins.php" class="btn-cancel">Cancel</a>

    </form>

</div>

</body>
</html>