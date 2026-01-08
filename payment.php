<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['customer_username'])) {
    header("Location: ../customer_login.php");
    exit();
}

// Get data from booking.php (via GET)
$room_name  = $_GET['room_name'] ?? '';
$room_price = isset($_GET['room_price']) ? floatval($_GET['room_price']) : 0;
$checkin    = $_GET['check_in'] ?? '';
$checkout   = $_GET['check_out'] ?? '';
$adults     = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
$children   = isset($_GET['children']) ? intval($_GET['children']) : 0;

$nights = 0;
$checkinFormatted = '';
$checkoutFormatted = '';

if (!empty($checkin) && !empty($checkout)) {
    $checkinFormatted  = date('d M', strtotime($checkin));
    $checkoutFormatted = date('d M', strtotime($checkout));
    $nights = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);
}

$total_price = $nights * $room_price; // just room price × nights



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">



<h2>Payment for: <?php echo htmlspecialchars($room_name); ?></h2>
<p>Total Amount: RM <?php echo htmlspecialchars($total_price); ?></p>

<?php if ($nights > 0): ?>
    <p style="font-weight:600; margin-bottom:6px;">
        Date: <?php echo $checkinFormatted; ?> / <?php echo $checkoutFormatted; ?>
        (<?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?>)
    </p>
    <p style="font-weight:600; margin-bottom:6px;">
        Guests: <?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?> / <?php echo $children; ?> Child<?php echo $children > 1 ? 'ren' : ''; ?>
    </p>
    <p style="font-weight:600; margin-bottom:6px;">
        Price: RM <?php echo $room_price; ?> × <?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?> = RM <?php echo $total_price; ?>
    </p>
<?php else: ?>
    <p style="font-weight:600; margin-bottom:6px;">Dates not available</p>
<?php endif; ?>



<h2>Available Payment Method:</h2>


    <div class="tabs">
        <button class="tab-button active" data-target="credit">💳 Credit / Debit Card</button>
        <button class="tab-button" data-target="bank">🔒 Online Banking</button>
        <button class="tab-button" data-target="wallet">📱 eWallet</button>
    </div>

    <!-- CREDIT CARD Section -->
    <div class="tab-content active" id="credit">
        <h3>Credit Card Details</h3>

        <form id="creditForm" method="POST" action="">

            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
            <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room_name); ?>">

            <label>Cardholder Name</label>
            <input type="text" id="cardName" placeholder="Enter cardholder name"required>

            <label>Credit Card Number</label>
            <input type="text" id="cardNumber" maxlength="16" placeholder="XXXX XXXX XXXX XXXX"pattern="\d{16}" required>

            <label>CVC / CVV</label>
            <input type="text" id="cvv" maxlength="4" placeholder="3–4 digit"pattern="\d{3,4}" required>

            <label>Expiry Date</label>
<div class="row">
    <select name="month" id="month" required>
        <option value="">Month</option>
        <?php
            for ($m = 1; $m <= 12; $m++) {
                $month = str_pad($m, 2, '0', STR_PAD_LEFT);
                echo "<option value='$month'>$month</option>";
            }
        ?>
    </select>

    <select name="year" id="year" required>
        <option value="">Year</option>
        <?php
            $currentYear = date("Y");
            $maxYear = $currentYear + 10;
            for ($y = $currentYear; $y <= $maxYear; $y++) {
                echo "<option value='$y'>$y</option>";
            }
        ?>
    </select>
</div>

<script>
// Disable past months if current year is selected
const monthSelect = document.getElementById('month');
const yearSelect = document.getElementById('year');

yearSelect.addEventListener('change', () => {
    const selectedYear = parseInt(yearSelect.value);
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1; // JS months: 0-11

    for (let i = 0; i < monthSelect.options.length; i++) {
        const monthValue = parseInt(monthSelect.options[i].value);
        if (selectedYear === currentYear) {
            // Disable past months
            if (monthValue < currentMonth) {
                monthSelect.options[i].disabled = true;
            } else if (monthValue >= currentMonth) {
                monthSelect.options[i].disabled = false;
            }
        } else {
            // All months enabled for future years
            monthSelect.options[i].disabled = false;
        }
    }

    // If selected month is now disabled, reset it
    if (monthSelect.value && monthSelect.options[monthSelect.selectedIndex].disabled) {
        monthSelect.value = '';
    }
});

function confirmCancel() {
    const userConfirmed = confirm("Are you sure you want to cancel the payment?");
    if (userConfirmed) {
        // Redirect user to the room booking page or wherever you want
        window.location.href = "bookingroom/roombooking.php";
    }
    // If user clicks "Cancel" in the confirm dialog, nothing happens
}


</script>


</div>


            <label>Card Issuing Country</label>
            <select id="country">
                <option value="">Select Country</option>
                <option>Malaysia</option>
                <option>Singapore</option>
                <option>Thailand</option>
            </select>

            <button type="submit" class="submit-btn">Proceed Payment</button>
<button type="button" class="cancel-btn" onclick="confirmCancel()">Cancel Payment</button>

        </form>
    </div>

    <!-- ONLINE BANKING SECTION -->
    <div class="tab-content" id="bank">
    <h3>Select Your Bank</h3>
    <ul class="bank-list">
        <li onclick="window.location.href='bank_login.html?bank=Maybank'">
            <img src="images/MayBank.png" alt="Maybank"> Maybank
        </li>
        <li onclick="window.location.href='bank_login.html?bank=CIMB'">
            <img src="images/CIMBBank.jpg" alt="CIMB"> CIMB
        </li>
        <li onclick="window.location.href='bank_login.html?bank=Public Bank'">
            <img src="images/Pbank.png" alt="Public Bank"> Public Bank
        </li>
        <li onclick="window.location.href='bank_login.html?bank=RHB'">
            <img src="images/RHBBank.jpg" alt="RHB"> RHB
        </li>
    </ul>
</div>


<!-- eWALLET SECTION -->
<div class="tab-content" id="wallet">
    <h3>Select Your eWallet</h3>
    <ul class="wallet-list">
        <li onclick="goQR('boost')">
            <img src="images/boost.jpg" alt="Boost" class="wallet-icon">
            Boost
        </li>
        <li onclick="goQR('grabpay')">
            <img src="images/grab.png" alt="GrabPay" class="wallet-icon">
            GrabPay
        </li>
        <li onclick="goQR('shopeepay')">
            <img src="images/shopee.png" alt="ShopeePay" class="wallet-icon">
            ShopeePay
        </li>
        <li onclick="goQR('tng')">
            <img src="images/tng.jpg" alt="Touch 'n Go Wallet" class="wallet-icon">
            Touch 'n Go Wallet
        </li>
    </ul>
    
</div>
 



</div>

<script src="script.js"></script>
</body>
</html>
