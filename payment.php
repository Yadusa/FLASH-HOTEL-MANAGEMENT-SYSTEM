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
    $nights = max(1, ceil((strtotime($checkout) - strtotime($checkin)) / 86400));
}

$total_price = $nights * $room_price;

// Map room images
$imageMap = [
    'Executive Suite' => 'luxury_suite.jpg',
    'Deluxe King Room' => 'deluxeroom.jpg',
    'Family Room' => 'familyroom.jpg',
    'Executive Deluxe King' => 'executive-deluxe-king.jpg',
    'Standard Double Room' => 'standarddouble.jpg',
    'Budget Twin Room' => 'budget.jpg'
];

$room_image = $imageMap[$room_name] ?? 'budget.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Page</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: url('<?= htmlspecialchars($room_image) ?>') center / cover no-repeat fixed;
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(10px);
}
.container {
    max-width: 700px;
    margin: 80px auto;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(4px);
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.3);
    padding: 30px;
    position: relative;
    z-index: 2;
}
h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    text-align: center;
    margin-bottom: 15px;
}
.tabs {
    display: flex;
    justify-content: space-around;
    margin: 20px 0;
    border-bottom: 2px solid #ddd;
}
.tab-button {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    color: #444;
}
.tab-button.active {
    border-bottom: 3px solid #1f2933;
    color: #1f2933;
}
.tab-content {
    display: none;
}
.tab-content.active { display: block; }
form label { display: block; margin: 10px 0 5px; }
form input, form select { width: 100%; padding: 10px 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
.row { display: flex; gap: 10px; }
.row select { flex: 1; }
.submit-btn, .cancel-btn {
    width: 48%; padding: 12px; margin-top: 10px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer;
}
.submit-btn { background: #1f2933; color: #fff; }
.submit-btn:hover { background:#374151; }
.cancel-btn { background: #e5e7eb; }
.cancel-btn:hover { background:#d1d5db; }

/* Modal */
#bookingModal {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;
}
#bookingModal .modal-content {
    background: #fff; padding: 30px; border-radius: 15px; max-width: 400px; text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
#bookingModal button { margin-top: 20px; padding: 12px 20px; border: none; background: #1f2933; color: #fff; border-radius: 10px; cursor: pointer; }

/* BANK & WALLET LIST STYLING */
.bank-list, .wallet-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    list-style: none;
    padding: 0;
    margin-top: 15px;
}

.bank-list li, .wallet-list li {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #f9fafb;
    border-radius: 15px;
    padding: 15px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    text-align: center;
    border: 1px solid #e5e7eb;
}

.bank-list li:hover, .wallet-list li:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    background: #fff;
}

.bank-list li img, .wallet-list li img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    margin-bottom: 10px;
    border-radius: 10px;
}

.bank-list li span, .wallet-list li span {
    margin-top: 5px;
    font-weight: 600;
    color: #1f2933;
}

/* QR MODAL */
#qrModal {
    display: none; /* hidden by default */
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center; align-items: center;
    z-index: 1000;
}

#qrModal .modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

</style>
</head>
<body>

<div class="container">
<h2>Payment for: <?= htmlspecialchars($room_name) ?></h2>

<h2>Available Payment Method:</h2>
<div class="tabs">
    <button class="tab-button active" data-target="credit">💳 Credit / Debit Card</button>
    <button class="tab-button" data-target="bank">🔒 Online Banking</button>
    <button class="tab-button" data-target="wallet">📱 eWallet</button>
</div>

<!-- CREDIT CARD FORM -->
<div class="tab-content active" id="credit">
    <h3>Credit Card Details</h3>

    <form id="creditForm" onsubmit="return confirmPayment()">

        <!-- CARD TYPE -->
        <label>Card Type</label>
        <div class="row" style="margin-bottom:15px;">
            <label style="flex:1; cursor:pointer;">
                <input type="radio" name="cardType" value="visa" onchange="setCardType('visa')" required>
                <img src="images/visa.png" style="height:35px; vertical-align:middle;">
                Visa
            </label>

            <label style="flex:1; cursor:pointer;">
                <input type="radio" name="cardType" value="master" onchange="setCardType('master')" required>
                <img src="images/mastercard.png" style="height:35px; vertical-align:middle;">
                MasterCard
            </label>
        </div>

        <!-- CARDHOLDER -->
        <label>Cardholder Name</label>
        <input type="text" id="cardName" placeholder="Enter cardholder name" required>

        <!-- CARD NUMBER -->
        <label>Credit Card Number</label>
        <input type="text"
               id="cardNumber"
               maxlength="16"
               placeholder="XXXX XXXX XXXX XXXX"
               inputmode="numeric"
               oninput="validateCardNumber(this)"
               required>

        <!-- CVV -->
        <label>CVC / CVV</label>
        <input type="text"
               id="cvv"
               maxlength="4"
               placeholder="3–4 digit"
               inputmode="numeric"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
               required>

        <!-- EXPIRY -->
        <label>Expiry Date</label>
        <div class="row">
            <select id="month" required>
                <option value="">Month</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month = str_pad($m, 2, '0', STR_PAD_LEFT);
                    echo "<option value='$month'>$month</option>";
                }
                ?>
            </select>

            <select id="year" required>
                <option value="">Year</option>
                <?php
                $currentYear = date("Y");
                for ($y = $currentYear; $y <= $currentYear + 5; $y++) {
                    echo "<option value='$y'>$y</option>";
                }
                ?>
            </select>
        </div>

        <!-- COUNTRY -->
        <label>Card Issuing Country</label>
        <select id="country" required>
            <option value="">Select Country</option>
            <option>Malaysia</option>
            <option>Singapore</option>
            <option>Thailand</option>
        </select>

        <button type="submit" class="submit-btn">Proceed Payment</button>
        <button type="button" class="cancel-btn" onclick="confirmCancel()">Cancel Payment</button>
    </form>
</div>


<!-- ONLINE BANKING Section -->
<div class="tab-content" id="bank">
    <h3>Select Your Bank</h3>
    <ul class="bank-list">
        <li onclick="window.location.href='bank_login.html?bank=Maybank'">
            <img src="images/MayBank.png" alt="Maybank">
            <span>Maybank</span>
        </li>
        <li onclick="window.location.href='bank_login.html?bank=CIMB'">
            <img src="images/CIMBBank.jpg" alt="CIMB">
            <span>CIMB</span>
        </li>
        <li onclick="window.location.href='bank_login.html?bank=Public Bank'">
            <img src="images/Pbank.png" alt="Public Bank">
            <span>Public Bank</span>
        </li>
        <li onclick="window.location.href='bank_login.html?bank=RHB'">
            <img src="images/RHBBank.jpg" alt="RHB">
            <span>RHB</span>
        </li>
    </ul>
</div>


<!-- eWALLET Section -->
<div class="tab-content" id="wallet">
    <h3>Select Your eWallet</h3>
    <ul class="wallet-list">
        <li onclick="showQR('Boost', 'images/QRcode.png')">
            <img src="images/boost.jpg" alt="Boost">
            <span>Boost</span>
        </li>
        <li onclick="showQR('GrabPay', 'images/QRcode.png')">
            <img src="images/grab.png" alt="GrabPay">
            <span>GrabPay</span>
        </li>
        <li onclick="showQR('ShopeePay', 'images/QRcode.png')">
            <img src="images/shopee.png" alt="ShopeePay">
            <span>ShopeePay</span>
        </li>
        <li onclick="showQR('Touch \'n Go', 'images/QRcode.png')">
            <img src="images/tng.jpg" alt="Touch 'n Go Wallet">
            <span>Touch 'n Go</span>
        </li>
    </ul>
</div>

<!-- QR CODE MODAL -->
<div id="qrModal" class="flex">
    <div class="modal-content" style="max-width:350px;">
        <h2 id="qrTitle"></h2>
        <img id="qrImage" src="" alt="QR Code" style="width:150px;height:150px;margin:20px 0;">
        <p>Scan QR to pay</p>
        <p>Time left: <span id="countdown">15</span>s</p>
    </div>
</div>


</div>

<!-- MODAL -->
<div id="bookingModal" class="flex">
    <div class="modal-content">
        <h2>Booking Summary</h2>
        <p id="modalRoom"></p>
        <p id="modalDate"></p>
        <p id="modalGuests"></p>
        <p id="modalTotal"></p>
        <button onclick="finishBooking()">Done</button>
    </div>
</div>

<script>
// TAB SWITCH
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
tabButtons.forEach(btn => btn.addEventListener('click', () => {
    tabButtons.forEach(b => b.classList.remove('active'));
    tabContents.forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.getAttribute('data-target')).classList.add('active');
}));

// CONFIRM PAYMENT
function confirmPayment() {
if (!selectedCardType) {
        alert("Please select Visa or MasterCard");
        return false;
    }
    const form = document.getElementById('creditForm');
    if (!form.checkValidity()) { form.reportValidity(); return false; }

    // Fill modal with info
    document.getElementById('modalRoom').innerText = "Room: <?= htmlspecialchars($room_name) ?>";
    document.getElementById('modalDate').innerText = "Date: <?= $checkinFormatted ?> → <?= $checkoutFormatted ?> (<?= $nights ?> night<?= $nights>1?'s':'' ?>)";
    document.getElementById('modalGuests').innerText = "Guests: <?= $adults ?> Adult<?= $adults>1?'s':'' ?> / <?= $children ?> Child<?= $children>1?'ren':'' ?>";
    document.getElementById('modalTotal').innerText = "Total Paid: RM <?= number_format($total_price,2,'.','') ?>";

    document.getElementById('bookingModal').style.display = "flex";

    return false; // prevent form submission
}

// DONE BUTTON
function finishBooking() {
    alert('Booking successfully!');
    window.location.href = 'bookingroom/roombooking.php';
}

// CANCEL BUTTON
function confirmCancel() {
    if (confirm("Are you sure you want to cancel the payment?")) {
        window.location.href = "bookingroom/roombooking.php";
    }
}

// SHOW QR MODAL
function showQR(walletName, walletImg) {
    // Set eWallet name and image
    document.getElementById('qrTitle').innerText = walletName;
    document.getElementById('qrImage').src = walletImg; // fake QR code
    document.getElementById('countdown').innerText = 15;

    // Show modal
    document.getElementById('qrModal').style.display = 'flex';

    // Countdown timer
    let timeLeft = 15;
    const timer = setInterval(() => {
        timeLeft--;
        document.getElementById('countdown').innerText = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timer); // stop timer
            document.getElementById('qrModal').style.display = 'none'; // hide QR modal
            showBookingSummary(); // show booking summary
        }
    }, 1000);
}

// SHOW BOOKING SUMMARY AFTER QR
function showBookingSummary() {
    // Fill modal with info
    document.getElementById('modalRoom').innerText = "Room: <?= htmlspecialchars($room_name) ?>";
    document.getElementById('modalDate').innerText = "Date: <?= $checkinFormatted ?> → <?= $checkoutFormatted ?> (<?= $nights ?> night<?= $nights>1?'s':'' ?>)";
    document.getElementById('modalGuests').innerText = "Guests: <?= $adults ?> Adult<?= $adults>1?'s':'' ?> / <?= $children ?> Child<?= $children>1?'ren':'' ?>";
    document.getElementById('modalTotal').innerText = "Total Paid: RM <?= number_format($total_price,2,'.','') ?>";

    // Show booking summary modal
    document.getElementById('bookingModal').style.display = "flex";
}

let selectedCardType = null;

/* When user selects Visa or MasterCard */
function setCardType(type) {
    selectedCardType = type;
    document.getElementById('cardNumber').value = '';
}

/* Validate card number while typing */
function validateCardNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');

    if (!selectedCardType) return;

    // VISA → must start with 4
    if (selectedCardType === 'visa') {
        if (input.value.length === 1 && input.value[0] !== '4') {
            alert('Visa card number must start with 4');
            input.value = '';
        }
    }

    // MASTERCARD → 51–55 OR 2221–2720
    if (selectedCardType === 'master') {
        if (input.value.length >= 2) {
            const firstTwo = parseInt(input.value.substring(0, 2));
            const firstFour = parseInt(input.value.substring(0, 4));

            const valid =
                (firstTwo >= 51 && firstTwo <= 55) ||
                (firstFour >= 2221 && firstFour <= 2720);

            if (!valid) {
                alert('MasterCard must start with 51–55 or 2221–2720');
                input.value = '';
            }
        }
    }
}


</script>
</body>
</html>


