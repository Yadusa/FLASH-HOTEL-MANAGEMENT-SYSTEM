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
    <title>Edit Staff Account | The Obsidian</title>
    <link rel="stylesheet" href="subadmin.css">
</head>
<body>

<div class="sidebar">
    <h2>The Obsidian Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_subadmins.php" class="active">Manage Subadmins</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="form-header">
            <h2>Edit Staff Details</h2>
            <p>Updating account for: <strong><?php echo htmlspecialchars($data['username']); ?></strong></p>
        </div>

        <form action="update_subadmins_process.php" method="POST" class="admin-form">
            <input type="hidden" name="old_id" value="<?php echo $data['id']; ?>">

            <div class="form-group">
                <label for="id">Admin ID</label>
                <input type="number" id="id" name="id" value="<?php echo $data['id']; ?>" required>
                <small>The unique staff identification number.</small>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Account Status</label>
                <select id="status" name="status" required style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="suspended" <?php echo ($data['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                    <option value="terminated" <?php echo ($data['status'] == 'terminated') ? 'selected' : ''; ?>>Terminated</option>
                </select>
                <small>Inactive or Suspended users cannot log in.</small>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="manage_subadmins.php" class="btn-link">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>