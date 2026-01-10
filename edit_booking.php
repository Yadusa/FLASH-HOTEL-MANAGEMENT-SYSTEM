<?php
session_start();
require_once('db.php');

if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit; }

$id = $_GET['id'];
$res = $conn->query("SELECT * FROM bookings WHERE id = $id");
$booking = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room = $_POST['room_name'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $status = $_POST['payment_status'];

    $update_sql = "UPDATE bookings SET room_name=?, checkin=?, checkout=?, payment_status=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $room, $checkin, $checkout, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: bookings.php?msg=updated");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        .btn-save { background: #4c8bf5; color: white; border: none; padding: 10px 20px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="form-container">
            <h3>Edit Booking #<?php echo $id; ?></h3>
            <form method="POST">
                <div class="form-group">
                    <label>Room Name</label>
                    <input type="text" name="room_name" value="<?php echo $booking['room_name']; ?>">
                </div>
                <div class="form-group">
                    <label>Check-in</label>
                    <input type="date" name="checkin" value="<?php echo $booking['checkin']; ?>">
                </div>
                <div class="form-group">
                    <label>Check-out</label>
                    <input type="date" name="checkout" value="<?php echo $booking['checkout']; ?>">
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <option value="Pending" <?php if($booking['payment_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Paid" <?php if($booking['payment_status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                        <option value="Cancelled" <?php if($booking['payment_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn-save">Update Booking</button>
                <a href="bookings.php">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>