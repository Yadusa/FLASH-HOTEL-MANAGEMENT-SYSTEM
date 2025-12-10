<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin_id"]) || $_SESSION["admin_role"] !== "superadmin") {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM admins WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
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

    <h2>Edit Subadmin</h2>

    <form action="update_subadmins_process.php" method="POST" class="form-box">

        <label>ID</label>
        <input type="number" name="id" value="<?php echo $data['id']; ?>" required>

        <input type="hidden" name="old_id" value="<?php echo $data['id']; ?>">

        <label>Username</label>
        <input type="text" name="username" value="<?php echo $data['username']; ?>" required>

        <label>Password</label>
        <input type="text" name="password" value="<?php echo $data['password']; ?>" required>

        <button type="submit" class="btn-save">Update</button>

    </form>

</div>

</body>
</html>
