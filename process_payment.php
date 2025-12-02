<?php
require 'connect.php';
header('Content-Type: text/html; charset=utf-8');

$method = $_POST['method'] ?? 'credit_card'; // credit_card | online_bank | ewallet
$amount = floatval($_POST['amount'] ?? 0);
$reference_no = $_POST['reference_no'] ?? uniqid('ref_');

if ($method === 'credit_card') {
    $cardholder = trim($_POST['cardholder'] ?? '');
    $card_num   = preg_replace('/\D/','', $_POST['card_number'] ?? '');
    $cvv        = preg_replace('/\D/','', $_POST['cvv'] ?? '');
    $expiry_month = intval($_POST['expiry_month'] ?? 0);
    $expiry_year  = intval($_POST['expiry_year'] ?? 0);
    $issuing_bank = trim($_POST['issuing_bank'] ?? '');
    $issuing_country = trim($_POST['issuing_country'] ?? '');

    // Mask sensitive info before storing
    $card_mask = substr($card_num, 0, 4) . str_repeat('*', max(0, strlen($card_num)-8)) . substr($card_num, -4);
    $cvv_mask = str_repeat('*', strlen($cvv));

    // Here you WOULD call a payment gateway API.
    // We'll simulate success.
    $status = 'success';

    $stmt = $pdo->prepare("INSERT INTO payments
        (reference_no, amount, method, cardholder_name, card_mask, cvv_mask, expiry_month, expiry_year, issuing_bank, issuing_country, status)
        VALUES (:reference_no,:amount,:method,:cardholder,:card_mask,:cvv_mask,:expiry_month,:expiry_year,:issuing_bank,:issuing_country,:status)");
    $stmt->execute([
        ':reference_no'=>$reference_no,
        ':amount'=>$amount,
        ':method'=>'credit_card',
        ':cardholder'=>$cardholder,
        ':card_mask'=>$card_mask,
        ':cvv_mask'=>$cvv_mask,
        ':expiry_month'=>$expiry_month,
        ':expiry_year'=>$expiry_year,
        ':issuing_bank'=>$issuing_bank,
        ':issuing_country'=>$issuing_country,
        ':status'=>$status,
    ]);

    header("Location: success.php?ref=" . urlencode($reference_no));
    exit;
}

if ($method === 'ewallet') {
    // eWallet selected: create pending record and redirect to simulated eWallet page
    $provider = $_POST['ewallet_provider'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO payments (reference_no, amount, method, ewallet_provider, status) VALUES (:ref,:amt,:method,:prov,'pending')");
    $stmt->execute([
        ':ref'=>$reference_no,
        ':amt'=>$amount,
        ':method'=>'ewallet',
        ':prov'=>$provider
    ]);

    // Redirect to a simulated eWallet flow (local)
    header("Location: ewallet_handler.php?ref=".urlencode($reference_no)."&provider=".urlencode($provider));
    exit;
}

// fallback
http_response_code(400);
echo "Unsupported payment method.";
