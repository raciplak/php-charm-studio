<?php
/**
 * Paratika Payment Callback Handler
 * Paratika redirects here after 3D Secure verification (both success and failure)
 * 
 * Flow:
 * 1. Paratika POSTs transaction result data
 * 2. We update tbl_paratika_payment_results with the result
 * 3. If successful: create order in tbl_payment + tbl_order, update stock, clear cart
 * 4. Redirect customer to success or failure page
 */
ob_start();
session_start();
include("../../admin/inc/config.php");
include("../../admin/inc/functions.php");

// Load language
$i=1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    define('LANG_VALUE_'.$i, $row['lang_value']);
    $i++;
}

// Collect all POST data from Paratika
$callbackData = $_POST;

// Key fields from Paratika callback
$sessionToken = isset($_POST['SESSIONTOKEN']) ? $_POST['SESSIONTOKEN'] : (isset($_POST['sessionToken']) ? $_POST['sessionToken'] : '');
$responseCode = isset($_POST['responseCode']) ? $_POST['responseCode'] : '';
$responseMsg = isset($_POST['responseMsg']) ? $_POST['responseMsg'] : '';
$errorCode = isset($_POST['errorCode']) ? $_POST['errorCode'] : '';
$errorMsg = isset($_POST['errorMsg']) ? $_POST['errorMsg'] : '';
$pgOrderId = isset($_POST['pgOrderId']) ? $_POST['pgOrderId'] : '';
$pgTranId = isset($_POST['pgTranId']) ? $_POST['pgTranId'] : '';
$pgTranRefId = isset($_POST['pgTranRefId']) ? $_POST['pgTranRefId'] : '';
$pgTranApprCode = isset($_POST['pgTranApprCode']) ? $_POST['pgTranApprCode'] : '';
$pgTranDate = isset($_POST['pgTranDate']) ? $_POST['pgTranDate'] : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : '0';
$currency = isset($_POST['currency']) ? $_POST['currency'] : 'TRY';
$installment = isset($_POST['installmentCount']) ? $_POST['installmentCount'] : '1';
$merchantPaymentId = isset($_POST['merchantPaymentId']) ? $_POST['merchantPaymentId'] : (isset($_POST['MERCHANTPAYMENTID']) ? $_POST['MERCHANTPAYMENTID'] : '');
$cardHolderName = isset($_POST['cardHolderName']) ? $_POST['cardHolderName'] : '';
$cardNumber4 = isset($_POST['maskedCardNumber']) ? $_POST['maskedCardNumber'] : '';
$paymentSystem = isset($_POST['paymentSystem']) ? $_POST['paymentSystem'] : '';
$binCardBrand = isset($_POST['cardBrand']) ? $_POST['cardBrand'] : '';
$binCardType = isset($_POST['cardType']) ? $_POST['cardType'] : '';
$binCardNetwork = isset($_POST['cardNetwork']) ? $_POST['cardNetwork'] : '';
$binIssuer = isset($_POST['issuer']) ? $_POST['issuer'] : '';

// Determine if sessionToken lookup should use POST or find by merchantPaymentId
$lookupField = 'session_token';
$lookupValue = $sessionToken;
if(empty($sessionToken) && !empty($merchantPaymentId)) {
    $lookupField = 'merchant_payment_id';
    $lookupValue = $merchantPaymentId;
}

if(empty($lookupValue)) {
    // No valid identifier - redirect to failure
    header('location: ../../payment_failed.php?error=' . urlencode('Geçersiz ödeme oturumu'));
    exit;
}

// Update the payment result record
$statement = $pdo->prepare("UPDATE tbl_paratika_payment_results SET 
    response_code=?, response_msg=?, error_code=?, error_msg=?,
    pg_order_id=?, pg_tran_id=?, pg_tran_ref_id=?, pg_tran_appr_code=?, pg_tran_date=?,
    amount=?, currency=?, installment=?,
    card_holder_name=COALESCE(NULLIF(?, ''), card_holder_name),
    card_number_first_last_4digit=COALESCE(NULLIF(?, ''), card_number_first_last_4digit),
    payment_system=?, bin_card_brand=?, bin_card_type=?, bin_card_network=?, bin_issuer=?,
    callback_data=?, updated_at=NOW()
    WHERE {$lookupField}=?");
$statement->execute(array(
    $responseCode, $responseMsg, $errorCode, $errorMsg,
    $pgOrderId, $pgTranId, $pgTranRefId, $pgTranApprCode, $pgTranDate,
    $amount, $currency, $installment,
    $cardHolderName, $cardNumber4,
    $paymentSystem, $binCardBrand, $binCardType, $binCardNetwork, $binIssuer,
    json_encode($callbackData),
    $lookupValue
));

// Check if payment was successful
$isSuccess = ($responseCode === '00' || $responseCode === '0');

if($isSuccess && isset($_SESSION['customer']) && isset($_SESSION['cart_p_id'])) {
    // ===== CREATE ORDER (same logic as bank/init.php) =====
    $payment_date = date('Y-m-d H:i:s');
    $payment_id = time();

    // Get the total from session cart
    $cart_total = 0;
    $i=0;
    foreach($_SESSION['cart_p_current_price'] as $key => $value) {
        $i++;
        $arr_cart_p_current_price[$i] = $value;
    }
    $i=0;
    foreach($_SESSION['cart_p_qty'] as $key => $value) {
        $i++;
        $arr_cart_p_qty[$i] = $value;
    }
    for($j=1; $j<=count($arr_cart_p_current_price); $j++) {
        $cart_total += $arr_cart_p_current_price[$j] * $arr_cart_p_qty[$j];
    }

    // Insert into tbl_payment
    $statement = $pdo->prepare("INSERT INTO tbl_payment (   
        customer_id, customer_name, customer_email,
        payment_date, txnid, paid_amount,
        card_number, card_cvv, card_month, card_year,
        bank_transaction_info, payment_method,
        payment_status, shipping_status, payment_id
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $statement->execute(array(
        $_SESSION['customer']['cust_id'],
        $_SESSION['customer']['cust_name'],
        $_SESSION['customer']['cust_email'],
        $payment_date,
        $pgTranId,
        $amount,
        $cardNumber4,
        '',
        '',
        '',
        'Paratika 3D Secure - Onay: ' . $pgTranApprCode,
        'Kredi Kartı',
        'Completed',
        'Pending',
        $payment_id
    ));

    // Update paratika results with payment_id
    $statement = $pdo->prepare("UPDATE tbl_paratika_payment_results SET payment_id=? WHERE {$lookupField}=?");
    $statement->execute(array($payment_id, $lookupValue));

    // Prepare cart arrays
    $i=0; foreach($_SESSION['cart_p_id'] as $key => $value) { $i++; $arr_cart_p_id[$i] = $value; }
    $i=0; foreach($_SESSION['cart_p_name'] as $key => $value) { $i++; $arr_cart_p_name[$i] = $value; }
    $i=0; foreach($_SESSION['cart_size_name'] as $key => $value) { $i++; $arr_cart_size_name[$i] = $value; }
    $i=0; foreach($_SESSION['cart_color_name'] as $key => $value) { $i++; $arr_cart_color_name[$i] = $value; }

    // Get all products for stock update
    $i=0;
    $statement = $pdo->prepare("SELECT * FROM tbl_product");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $i++;
        $arr_p_id[$i] = $row['p_id'];
        $arr_p_qty[$i] = $row['p_qty'];
    }

    // Insert order items and update stock
    for($i=1; $i<=count($arr_cart_p_name); $i++) {
        $statement = $pdo->prepare("INSERT INTO tbl_order (
            product_id, product_name, size, color, quantity, unit_price, payment_id
        ) VALUES (?,?,?,?,?,?,?)");
        $statement->execute(array(
            $arr_cart_p_id[$i],
            $arr_cart_p_name[$i],
            $arr_cart_size_name[$i],
            $arr_cart_color_name[$i],
            $arr_cart_p_qty[$i],
            $arr_cart_p_current_price[$i],
            $payment_id
        ));

        // Update stock
        for($j=1; $j<=count($arr_p_id); $j++) {
            if($arr_p_id[$j] == $arr_cart_p_id[$i]) {
                $current_qty = $arr_p_qty[$j];
                break;
            }
        }
        $final_quantity = $current_qty - $arr_cart_p_qty[$i];
        $statement = $pdo->prepare("UPDATE tbl_product SET p_qty=? WHERE p_id=?");
        $statement->execute(array($final_quantity, $arr_cart_p_id[$i]));
    }

    // Clear cart session
    unset($_SESSION['cart_p_id']);
    unset($_SESSION['cart_size_id']);
    unset($_SESSION['cart_size_name']);
    unset($_SESSION['cart_color_id']);
    unset($_SESSION['cart_color_name']);
    unset($_SESSION['cart_p_qty']);
    unset($_SESSION['cart_p_current_price']);
    unset($_SESSION['cart_p_name']);
    unset($_SESSION['cart_p_featured_photo']);

    // Redirect to success
    header('location: ../../payment_success.php');
    exit;

} elseif($isSuccess) {
    // Payment successful but no session/cart (maybe session expired)
    header('location: ../../payment_success.php');
    exit;
} else {
    // Payment failed
    $failMsg = !empty($errorMsg) ? $errorMsg : (!empty($responseMsg) ? $responseMsg : 'Ödeme başarısız oldu');
    header('location: ../../payment_failed.php?error=' . urlencode($failMsg) . '&code=' . urlencode($errorCode));
    exit;
}
?>
