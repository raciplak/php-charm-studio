<?php
/**
 * Paratika Payment - Session Token Creation & 3D Secure Redirect
 * Adapted from B2B Paratika edge function to PHP
 * 
 * Flow:
 * 1. Customer submits credit card form from checkout
 * 2. This script creates a SESSIONTOKEN via Paratika API
 * 3. Auto-submits card data to Paratika Direct POST 3D URL
 * 4. Customer completes 3D verification on bank page
 * 5. Paratika POSTs result to callback.php
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

// Validate customer session
if(!isset($_SESSION['customer'])) {
    header('location: ../../login.php');
    exit;
}

// Validate cart
if(!isset($_SESSION['cart_p_id']) || count($_SESSION['cart_p_id']) == 0) {
    header('location: ../../cart.php');
    exit;
}

// Validate POST data
if(!isset($_POST['card_holder']) || !isset($_POST['card_number']) || !isset($_POST['expiry_month']) || !isset($_POST['expiry_year']) || !isset($_POST['cvv']) || !isset($_POST['amount'])) {
    header('location: ../../checkout.php');
    exit;
}

// Get Paratika settings
$statement = $pdo->prepare("SELECT * FROM tbl_payment_gateway_settings WHERE gateway_name='paratika' AND is_active=1 LIMIT 1");
$statement->execute();
$settings = $statement->fetch(PDO::FETCH_ASSOC);

if(!$settings) {
    die('Ödeme sistemi yapılandırılmamış. Lütfen yönetici ile iletişime geçin.');
}

$amount = floatval($_POST['amount']);
$cardHolder = trim($_POST['card_holder']);
$cardNumber = preg_replace('/\s+/', '', $_POST['card_number']);
$expiryMonth = trim($_POST['expiry_month']);
$expiryYear = trim($_POST['expiry_year']);
$cvv = trim($_POST['cvv']);

// Ensure 4-digit year
if(strlen($expiryYear) == 2) {
    $expiryYear = '20' . $expiryYear;
}

$customerEmail = $_SESSION['customer']['cust_email'];
$customerPhone = isset($_SESSION['customer']['cust_phone']) ? $_SESSION['customer']['cust_phone'] : '';
$customerName = $_SESSION['customer']['cust_name'];
$customerId = $_SESSION['customer']['cust_id'];

$merchantPaymentId = 'MERPA-' . time() . '-' . substr(md5(rand()), 0, 6);
$callbackUrl = BASE_URL . 'payment/paratika/callback.php';

// Client IP
$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if(strpos($clientIp, ',') !== false) {
    $clientIp = trim(explode(',', $clientIp)[0]);
}

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0';

// Order items JSON
$orderItems = json_encode(array(array(
    'code' => 'PAYMENT',
    'name' => 'Siparis Odemesi',
    'description' => 'E-Ticaret Siparis Odemesi',
    'quantity' => 1,
    'amount' => number_format($amount, 2, '.', '')
)));

// Billing/Shipping info from session
$billAddress = isset($_SESSION['customer']['cust_b_address']) ? $_SESSION['customer']['cust_b_address'] : '';
$billCity = isset($_SESSION['customer']['cust_b_city']) ? $_SESSION['customer']['cust_b_city'] : '';
$billZip = isset($_SESSION['customer']['cust_b_zip']) ? $_SESSION['customer']['cust_b_zip'] : '';
$billPhone = isset($_SESSION['customer']['cust_b_phone']) ? $_SESSION['customer']['cust_b_phone'] : $customerPhone;
$shipAddress = isset($_SESSION['customer']['cust_s_address']) ? $_SESSION['customer']['cust_s_address'] : $billAddress;
$shipCity = isset($_SESSION['customer']['cust_s_city']) ? $_SESSION['customer']['cust_s_city'] : $billCity;
$shipZip = isset($_SESSION['customer']['cust_s_zip']) ? $_SESSION['customer']['cust_s_zip'] : $billZip;
$shipPhone = isset($_SESSION['customer']['cust_s_phone']) ? $_SESSION['customer']['cust_s_phone'] : $billPhone;

// Step 1: Create SESSIONTOKEN
$sessionParams = array(
    'ACTION' => 'SESSIONTOKEN',
    'SESSIONTYPE' => 'PAYMENTSESSION',
    'AMOUNT' => number_format($amount, 2, '.', ''),
    'CURRENCY' => 'TRY',
    'MERCHANTPAYMENTID' => $merchantPaymentId,
    'MERCHANTUSER' => $settings['api_username'],
    'MERCHANTPASSWORD' => $settings['api_password'],
    'MERCHANT' => $settings['merchant_id'],
    'CUSTOMER' => $customerEmail,
    'CUSTOMERNAME' => $customerName,
    'CUSTOMEREMAIL' => $customerEmail,
    'CUSTOMERIP' => $clientIp,
    'CUSTOMERUSERAGENT' => $userAgent,
    'CUSTOMERPHONE' => $billPhone,
    'BILLTOADDRESSLINE' => $billAddress,
    'BILLTOCITY' => $billCity,
    'BILLTOCOUNTRY' => 'TR',
    'BILLTOPOSTALCODE' => $billZip,
    'BILLTOPHONE' => $billPhone,
    'SHIPTOADDRESSLINE' => $shipAddress,
    'SHIPTOCITY' => $shipCity,
    'SHIPTOCOUNTRY' => 'TR',
    'SHIPTOPOSTALCODE' => $shipZip,
    'SHIPTOPHONE' => $shipPhone,
    'RETURNURL' => $callbackUrl,
    'ERRORURL' => $callbackUrl,
    'ORDERITEMS' => $orderItems,
);

$ch = curl_init($settings['api_url']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sessionParams));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if($curlError) {
    die('Ödeme sunucusuna bağlanılamadı: ' . htmlspecialchars($curlError));
}

$sessionResult = json_decode($response, true);

if(!$sessionResult || $sessionResult['responseCode'] !== '00' || empty($sessionResult['sessionToken'])) {
    $errorMsg = isset($sessionResult['errorMsg']) ? $sessionResult['errorMsg'] : 'Oturum oluşturulamadı';
    $errorCode = isset($sessionResult['errorCode']) ? $sessionResult['errorCode'] : 'UNKNOWN';
    die('Ödeme hatası [' . htmlspecialchars($errorCode) . ']: ' . htmlspecialchars($errorMsg));
}

$sessionToken = $sessionResult['sessionToken'];

// Build Direct POST 3D URL
$postUrl = str_replace('{SESSIONTOKEN}', $sessionToken, $settings['direct_post_3d_url']);

// Store masked card number
$maskedCard = '';
if(strlen($cardNumber) >= 8) {
    $maskedCard = substr($cardNumber, 0, 4) . '****' . substr($cardNumber, -4);
}

// Store initial payment record in DB
$statement = $pdo->prepare("INSERT INTO tbl_paratika_payment_results 
    (session_token, merchant_payment_id, response_code, response_msg, amount, currency, 
     card_holder_name, card_number_first_last_4digit, customer_id, customer_name, customer_email, callback_data)
    VALUES (?, ?, 'PENDING', 'Payment initiated', ?, 'TRY', ?, ?, ?, ?, ?, ?)");
$statement->execute(array(
    $sessionToken,
    $merchantPaymentId,
    $amount,
    $cardHolder,
    $maskedCard,
    $customerId,
    $customerName,
    $customerEmail,
    json_encode(array('initiated_at' => date('Y-m-d H:i:s'), 'client_ip' => $clientIp))
));

// Step 2: Auto-submit form to Paratika Direct POST 3D
// This sends card data directly to Paratika (never stored on our server)
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>3D Secure Yönlendirme...</title>
    <style>
        body { 
            display: flex; align-items: center; justify-content: center; 
            min-height: 100vh; margin: 0; 
            font-family: Arial, sans-serif; background: #f5f5f5; 
        }
        .loading-box {
            text-align: center; background: #fff; padding: 40px 60px;
            border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .spinner {
            width: 40px; height: 40px; margin: 0 auto 20px;
            border: 4px solid #e0e0e0; border-top-color: #e67e22;
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { color: #333; margin: 0 0 10px; font-size: 18px; }
        p { color: #666; margin: 0; font-size: 14px; }
    </style>
</head>
<body>
    <div class="loading-box">
        <div class="spinner"></div>
        <h2>3D Secure Doğrulamasına Yönlendiriliyorsunuz</h2>
        <p>Lütfen bekleyin, bankanızın güvenlik sayfasına yönlendiriliyorsunuz...</p>
    </div>

    <form id="paratika3dForm" action="<?php echo htmlspecialchars($postUrl); ?>" method="POST" style="display:none;">
        <input type="hidden" name="cardOwner" value="<?php echo htmlspecialchars($cardHolder); ?>">
        <input type="hidden" name="pan" value="<?php echo htmlspecialchars($cardNumber); ?>">
        <input type="hidden" name="expiryMonth" value="<?php echo htmlspecialchars($expiryMonth); ?>">
        <input type="hidden" name="expiryYear" value="<?php echo htmlspecialchars($expiryYear); ?>">
        <input type="hidden" name="cvv" value="<?php echo htmlspecialchars($cvv); ?>">
        <input type="hidden" name="installmentCount" value="1">
    </form>

    <script>
        document.getElementById('paratika3dForm').submit();
    </script>
</body>
</html>
<?php exit; ?>
