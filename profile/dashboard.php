<?php
session_start();
include "../db_customer.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* Fetch customer info */
$stmt = $conn->prepare("SELECT * FROM customer WHERE id=?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Fetch booking info */
$stmtBooking = $conn->prepare("
    SELECT * FROM booking 
    WHERE customer_id=? 
    ORDER BY created_at DESC
");
$stmtBooking->bind_param("i", $customer_id);
$stmtBooking->execute();
$bookings = $stmtBooking->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f5f7;
    margin: 0;
}

/* Layout */
.dashboard {
    max-width: 1200px;
    margin: 50px auto;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 25px;
}

/* Sidebar */
.sidebar {
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

.sidebar h3 {
    margin-bottom: 15px;
}

.sidebar p {
    font-size: 14px;
    color: #4b5563;
}

.sidebar a {
    display: block;
    margin-top: 12px;
    padding: 10px;
    text-decoration: none;
    color: #374151;
    border-radius: 8px;
}

.sidebar a:hover {
    background: #e0e7ff;
    color: #4f46e5;
}

/* Main content */
.main {
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(0,0,0,0.1);
}

.main h2 {
    margin-top: 0;
}

/* Profile info */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.profile-box {
    background: #f9fafb;
    padding: 14px;
    border-radius: 10px;
    font-size: 14px;
}

.profile-box span {
    display: block;
    color: #6b7280;
    font-size: 12px;
}

/* Booking table */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

th {
    background: #f3f4f6;
}

tr:not(:last-child) {
    border-bottom: 1px solid #e5e7eb;
}

.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status.confirmed { background: #dcfce7; color: #166534; }
.status.pending { background: #fef3c7; color: #92400e; }
.status.cancelled { background: #fee2e2; color: #991b1b; }

/* Cancel button */
.cancel-btn {
    background: #ef4444;
    border: none;
    color: #fff;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
}

.cancel-btn:hover {
    background: #dc2626;
}

/* Responsive */
@media (max-width: 900px) {
    .dashboard {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<?php if (isset($_GET['cancelled'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Booking cancelled',
    text: 'Your booking has been successfully cancelled.',
    confirmButtonColor: '#4f46e5'
});
</script>
<?php endif; ?>


<body>

<div class="dashboard">

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Hello, <?= htmlspecialchars($user['cust_name']); ?></h3>
        <p><?= htmlspecialchars($user['cust_email']); ?></p>

        <a href="dashboard.php">Dashboard</a>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="change_password.php">Change Password</a>
        <a href="../customer_logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h2>My Profile</h2>

        <div class="profile-grid">
            <div class="profile-box">
                <span>Username</span>
                <?= htmlspecialchars($user['username']); ?>
            </div>
            <div class="profile-box">
                <span>Contact</span>
                <?= htmlspecialchars($user['contact_number']); ?>
            </div>
            <div class="profile-box">
                <span>Email</span>
                <?= htmlspecialchars($user['cust_email']); ?>
            </div>
            <div class="profile-box">
                <span>Address</span>
                <?= htmlspecialchars($user['address']); ?>
            </div>
        </div>

        <h2>My Bookings</h2>

        <?php if ($bookings->num_rows === 0): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Room Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php while ($row = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['room_type']); ?></td>
                    <td><?= htmlspecialchars($row['check_in']); ?></td>
                    <td><?= htmlspecialchars($row['check_out']); ?></td>
                    <td>
                        <span class="status <?= strtolower($row['status']); ?>">
                            <?= ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] !== 'cancelled'): ?>
                            <form action="cancel_booking.php" method="POST" class="cancel-form">
    <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
    <button type="button" class="cancel-btn"
            onclick="confirmCancel(this.form)">
        Cancel
    </button>
</form>
<script>
function confirmCancel(form) {
    Swal.fire({
        title: 'Cancel booking?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Yes, cancel it',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

                                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>
