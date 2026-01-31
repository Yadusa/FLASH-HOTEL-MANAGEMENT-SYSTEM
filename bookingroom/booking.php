<?php
session_start();

// Redirect to login if not logged in
if(!isset($_SESSION['customer_username'])) {
    header("Location: ../customer_login.php");
    exit();
}

include '../db.php';

// Get room info
if(!isset($_GET['room_name']) || !isset($_GET['room_price'])) {
    die("Room information not provided.");
}

$room_name = $_GET['room_name'];
$room_price = $_GET['room_price'];

$today = date('Y-m-d'); 
$maxDate = date('Y-m-d', strtotime('+5 months')); 

// Booking submission
$success_message = '';
$total_price = 0;
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_username = $_SESSION['customer_username'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    $totalGuests = $adults + $children;

    // --- UPDATED VALIDATION & AVAILABILITY LOGIC ---

if ($adults < 1 || $adults > 5) {
    $error_message = "Number of adults must be between 1 and 5.";
} elseif ($totalGuests > 5) {
    $error_message = "Total guests (adults + children) cannot exceed 5 per room.";
} else {
    // 1. First, check if the room is globally blocked for Maintenance
    $status_stmt = $conn->prepare("SELECT room_status, total_slots FROM rooms WHERE room_name = ?");
    $status_stmt->bind_param("s", $room_name);
    $status_stmt->execute();
    $room_info = $status_stmt->get_result()->fetch_assoc();

    if ($room_info['room_status'] === 'Maintenance') {
        $error_message = "Sorry, this room is currently closed for maintenance.";
    } else {
        // 2. Check how many slots are occupied for the SELECTED DATES
        // This query counts any confirmed booking that overlaps with the user's dates
        $check_avail_sql = "SELECT COUNT(*) as occupied_slots FROM bookings 
                            WHERE room_name = ? 
                            AND payment_status != 'Cancelled'
                            AND (checkin < ? AND checkout > ?)";
        $avail_stmt = $conn->prepare($check_avail_sql);
        $avail_stmt->bind_param("sss", $room_name, $checkout, $checkin);
        $avail_stmt->execute();
        $occupied = $avail_stmt->get_result()->fetch_assoc()['occupied_slots'];

        if ($occupied >= $room_info['total_slots']) {
            // --- NEW: FETCH BLOCKED DATES TO SHOW CUSTOMER ---
            $block_info_sql = "SELECT DISTINCT checkin, checkout FROM bookings 
                   WHERE room_name = ? 
                   AND payment_status = 'Blocked' 
                   AND checkout >= CURDATE() 
                   ORDER BY checkin ASC";
            $block_stmt = $conn->prepare($block_info_sql);
            $block_stmt->bind_param("s", $room_name);
            $block_stmt->execute();
            $blocks = $block_stmt->get_result();

            if ($blocks->num_rows > 0) {
                $block_list = [];
                while ($b = $blocks->fetch_assoc()) {
                    $block_list[] = date('M d', strtotime($b['checkin'])) . " to " . date('M d, Y', strtotime($b['checkout']));
                }
                $error_message = "This room is unavailable for your selected dates. <br><strong>Upcoming blocked dates:</strong><br> " . implode("<br>", $block_list);
            } else {
                $error_message = "Sorry, this room is fully booked for your selected dates. Please try different dates.";
            }
        } else {
            // 3. PROCEED WITH BOOKING
            $diff = strtotime($checkout) - strtotime($checkin);
            $nights = max(1, ceil($diff / (60*60*24)));
            $total_price = $nights * $room_price;

            $stmt = $conn->prepare("INSERT INTO bookings (customer_username, room_name, room_price, checkin, checkout, adults, children, total_price, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')");
            
            // Note: I added 'Confirmed' to payment_status so the room logic detects it immediately
            $stmt->bind_param("ssdssiid", $customer_username, $room_name, $room_price, $checkin, $checkout, $adults, $children, $total_price);

            if ($stmt->execute()) {
                // SUCCESS: Redirect to confirmation
                header("Location: booking_confirm.php?room_name=" . urlencode($room_name) . "&room_price=$room_price&check_in=$checkin&check_out=$checkout&adults=$adults&children=$children");
                exit();
            } else {
                $error_message = "Error while booking: " . $conn->error;
            }
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Room | The Obsidian KL</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script>
function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}
</script>

<script>
// Step 3: Adjust checkout min based on selected check-in
const checkin = document.getElementById('checkin');
const checkout = document.getElementById('checkout');

checkin.addEventListener('change', () => {
    const checkinDate = new Date(checkin.value);
    const nextDay = new Date(checkinDate);
    nextDay.setDate(checkinDate.getDate() + 1); // checkout must be at least 1 day after check-in

    const yyyy = nextDay.getFullYear();
    const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
    const dd = String(nextDay.getDate()).padStart(2, '0');

    checkout.min = `${yyyy}-${mm}-${dd}`;

    // Optional: clear checkout if it's before new min
    if (checkout.value < checkout.min) {
        checkout.value = '';
    }
});
</script>



<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background-color: #f4f4f8;
    color: #333;
}

.top-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 15px 30px;
    background-color: #1f2933;
    color: #fff;
    font-weight: 500;
}
.top-bar a {
    color: #fff;
    margin-left: 15px;
    text-decoration: none;
}
.top-bar a:hover {
    text-decoration: underline;
}

.booking-container {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
    padding: 30px;
    text-align: center;
}

.booking-container h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: #1f2933;
}

.booking-container h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    margin: 10px 0;
    color: #111827;
}

.booking-container p {
    font-size: 1rem;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    text-align: left;
}

form label {
    font-weight: 500;
    margin-bottom: 5px;
}

form input {
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
    transition: 0.3s;
}
form input:focus {
    border-color: #1f2933;
    outline: none;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: 0.3s;
    border: none;
}

.btn-primary {
    background-color: #1f2933;
    color: #fff;
}
.btn-primary:hover {
    background-color: #374151;
}

.btn-secondary {
    background-color: #e5e7eb;
    color: #111827;
}
.btn-secondary:hover {
    background-color: #d1d5db;
}

.success-message {
    color: green;
    font-weight: 500;
    margin-bottom: 15px;
}

.error-message {
    color: red;
    font-weight: 500;
    margin-bottom: 15px;
}

@media (max-width: 640px) {
    .booking-container {
        margin: 20px;
        padding: 20px;
    }
    .booking-container h1 {
        font-size: 2rem;
    }
    .booking-container h2 {
        font-size: 1.5rem;
    }
}
</style>
</head>
<body>

<div class="top-bar">
    Welcome, <?php echo htmlspecialchars($_SESSION['customer_username']); ?> |
    <a href="../customer_logout.php" onclick="return confirmLogout();">Logout</a>

</div>

<div class="booking-container">
    <h1>Book Your Room</h1>
    <h2><?php echo htmlspecialchars($room_name); ?></h2>
    <p>Price: RM <?php echo htmlspecialchars($room_price); ?> / night</p>

    <?php if(!empty($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php elseif(!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
<form method="POST" action="">
    <label for="checkin">Check-in Date:</label>
<input type="date" name="checkin" id="checkin" required
       min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>">

<label for="checkout">Check-out Date:</label>
<input type="date" name="checkout" id="checkout" required
       min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>">


    <label for="adults">Number of Adults:</label>
<input type="number" name="adults" id="adults" min="1" max="5" value="1" required>

<label for="children">Number of Children:</label>
<input type="number" name="children" id="children" min="0" max="4" value="0" required>
<small id="child-note" style="color:#555;">Max children depends on number of adults (total guests ≤ 5)</small>

<script>
const adultsInput = document.getElementById('adults');
const childrenInput = document.getElementById('children');
const maxGuests = 5;

function updateChildrenMax() {
    const adults = parseInt(adultsInput.value) || 1;
    const maxChildren = maxGuests - adults; // remaining spots for children
    childrenInput.max = maxChildren;

    // Adjust value if current children exceeds max
    if (parseInt(childrenInput.value) > maxChildren) {
        childrenInput.value = maxChildren;
    }

    // Optional: show note dynamically
    document.getElementById('child-note').innerText =
        `Max children allowed: ${maxChildren} (total guests ≤ 5)`;
}

// Trigger when adults change
adultsInput.addEventListener('input', updateChildrenMax);
// Trigger when children change (to correct if needed)
childrenInput.addEventListener('input', updateChildrenMax);

// Initialize on page load
updateChildrenMax();
</script>


    <?php if($total_price > 0): ?>
        <p style="font-weight:600;">Total Price: RM <?php echo $total_price; ?></p>
        <p style="font-weight:600;">
    Adults: <?php echo $adults; ?>, Children: <?php echo $children; ?> (Total: <?php echo $guests; ?>)
</p>

    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Confirm Booking</button>
</form>


    <a href="roombooking.php" class="btn btn-secondary" style="margin-top:20px;">← Back to Rooms</a>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');

    // Update checkout min whenever check-in changes
    checkin.addEventListener('change', () => {
        if (!checkin.value) return;

        const checkinDate = new Date(checkin.value);
        const nextDay = new Date(checkinDate);
        nextDay.setDate(checkinDate.getDate() + 1); // checkout at least 1 day after check-in

        const yyyy = nextDay.getFullYear();
        const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
        const dd = String(nextDay.getDate()).padStart(2, '0');

        const minDate = `${yyyy}-${mm}-${dd}`;
        checkout.min = minDate;

        // Clear checkout if it's now before the new min
        if (checkout.value && checkout.value < checkout.min) {
            checkout.value = '';
        }
    });

    // Optional: set checkout default min to tomorrow if checkin is empty
    if (!checkin.value) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        const yyyy = tomorrow.getFullYear();
        const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const dd = String(tomorrow.getDate()).padStart(2, '0');
        checkout.min = `${yyyy}-${mm}-${dd}`;
    }
});
</script>


</body>
</html>
