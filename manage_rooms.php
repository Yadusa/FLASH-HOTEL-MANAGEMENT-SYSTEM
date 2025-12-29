<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION["admin_name"];
$adminRole = $_SESSION["admin_role"];

// Fetch rooms from database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms | FLASH Hotel Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --obsidian-gold: #b89241;
            --dark-sidebar: #2c3e50;
        }

        /* Styling to match the Room Details UI */
        .admin-luxury-header {
            font-family: 'Playfair Display', serif;
            color: var(--dark-sidebar);
            border-bottom: 2px solid var(--obsidian-gold);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .room-management-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            padding: 20px;
        }

        .admin-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-update {
            background-color: var(--obsidian-gold);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }

        .btn-update:hover {
            background-color: #9a7a35;
        }

        .room-table {
            width: 100%;
            border-collapse: collapse;
        }

        .room-table th {
            text-align: left;
            padding: 12px;
            background: #f8f9fa;
            border-bottom: 2px solid #eee;
        }

        .room-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .status-pill {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .available { background: #e3fcef; color: #006644; }
        .occupied { background: #ffebe6; color: #bf2600; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <h2> FLASH Hotel Admin</h2>
        <br><p class="role"><?php echo ucfirst($adminRole); ?></p>
    </div>
    <a href="dashboard.php"> Dashboard</a>
    <a href="manage_rooms.php" class="active"> Manage Rooms</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main-content">
    <div class="topbar">
        <h3 class="admin-luxury-header">Room Inventory Management</h3>
    </div>

    <div class="room-management-container">
        <div class="admin-card">
            <h4 style="font-family: 'Playfair Display'; margin-bottom: 20px;">Update Room Info</h4>
            <form action="process_room.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Room Name (e.g. Executive Suite)</label>
                    <input type="text" name="room_name" class="form-control" placeholder="Room Title">
                </div>
                <div class="form-group">
                    <label>Price (RM)</label>
                    <input type="number" name="price" class="form-control" placeholder="1000">
                </div>
                <div class="form-group">
                    <label>Room Status</label>
                    <select name="status" class="form-control">
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Upload Room Image</label>
                    <input type="file" name="room_img" class="form-control">
                </div>
                <button type="submit" class="btn-update">Update Room Listing</button>
            </form>
        </div>

        <div class="admin-card">
            <h4 style="font-family: 'Playfair Display'; margin-bottom: 20px;">Active Inventory</h4>
            <table class="room-table">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo $row['room_type']; ?></strong></td>
                        <td>RM <?php echo $row['price']; ?></td>
                        <td>
                            <span class="status-pill <?php echo ($row['room_status'] == 'Available') ? 'available' : 'occupied'; ?>">
                                <?php echo $row['room_status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" style="color: var(--obsidian-gold); text-decoration:none; font-weight:600;">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>