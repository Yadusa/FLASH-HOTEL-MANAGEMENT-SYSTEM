<?php
session_start();

// Redirect if not logged in
if(!isset($_SESSION['customer_username'])){
    header("Location: ../customer_login.php");
    exit();
}

// Get total price and room name from GET (sent from booking.php)
$total_price = isset($_GET['total_price']) ? $_GET['total_price'] : 0;
$room_name = isset($_GET['room_name']) ? $_GET['room_name'] : '';
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

    <h2>Available Payment Method:</h2>

    <div class="tabs">
        <button class="tab-button active" data-target="credit">💳 Credit / Debit Card</button>
        <button class="tab-button" data-target="bank">🔒 Online Banking</button>
        <button class="tab-button" data-target="wallet">📱 eWallet</button>
    </div>

    <!-- CREDIT CARD Section -->
    <div class="tab-content active" id="credit">
        <h3>Credit Card Details</h3>

        <form id="creditForm">

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
                <select id="month">
                    <option value="">Month</option>
                    <option>01</option><option>02</option><option>03</option><option>04</option>
                    <option>05</option><option>06</option><option>07</option><option>08</option>
                    <option>09</option><option>10</option><option>11</option><option>12</option>
                </select>

                <select id="year">
                    <option value="">Year</option>
                    <option>2025</option>
                    <option>2026</option>
                    <option>2027</option>
                    <option>2028</option>
                </select>
            </div>

            <label>Card Issuing Country</label>
            <select id="country">
                <option value="">Select Country</option>
                <option>Malaysia</option>
                <option>Singapore</option>
                <option>Thailand</option>
            </select>

            <button type="submit" class="submit-btn">Proceed Payment</button>
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

