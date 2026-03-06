<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$p_id = isset($_POST['p_id']) ? intval($_POST['p_id']) : 0;

if($p_id == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz ürün']);
    exit;
}

// Get product from DB
$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
$statement->execute(array($p_id));
$product = $statement->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Ürün bulunamadı']);
    exit;
}

$p_current_price = $product['p_current_price'];
$p_name = $product['p_name'];
$p_featured_photo = $product['p_featured_photo'];
$p_stock = $product['p_qty'];
$p_qty = 1;

if($p_stock < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Stokta yok']);
    exit;
}

$size_id = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;
$color_id = isset($_POST['color_id']) ? intval($_POST['color_id']) : 0;

// Get size/color names
$size_name = '';
$color_name = '';
if($size_id > 0) {
    $st = $pdo->prepare("SELECT size_name FROM tbl_size WHERE size_id=?");
    $st->execute(array($size_id));
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if($r) $size_name = $r['size_name'];
}
if($color_id > 0) {
    $st = $pdo->prepare("SELECT color_name FROM tbl_color WHERE color_id=?");
    $st->execute(array($color_id));
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if($r) $color_name = $r['color_name'];
}

// Check if already in cart
if(isset($_SESSION['cart_p_id'])) {
    foreach($_SESSION['cart_p_id'] as $key => $val) {
        if($val == $p_id && $_SESSION['cart_size_id'][$key] == $size_id && $_SESSION['cart_color_id'][$key] == $color_id) {
            echo json_encode(['status' => 'error', 'message' => 'Bu ürün zaten sepetinizde']);
            exit;
        }
    }
    
    $keys = array_keys($_SESSION['cart_p_id']);
    $new_key = max($keys) + 1;
} else {
    $new_key = 1;
}

$_SESSION['cart_p_id'][$new_key] = $p_id;
$_SESSION['cart_size_id'][$new_key] = $size_id;
$_SESSION['cart_size_name'][$new_key] = $size_name;
$_SESSION['cart_color_id'][$new_key] = $color_id;
$_SESSION['cart_color_name'][$new_key] = $color_name;
$_SESSION['cart_p_qty'][$new_key] = $p_qty;
$_SESSION['cart_p_current_price'][$new_key] = $p_current_price;
$_SESSION['cart_p_name'][$new_key] = $p_name;
$_SESSION['cart_p_featured_photo'][$new_key] = $p_featured_photo;

// Calculate new totals
$cart_count = count($_SESSION['cart_p_id']);
$cart_total = 0;
foreach($_SESSION['cart_p_qty'] as $k => $q) {
    $cart_total += $q * $_SESSION['cart_p_current_price'][$k];
}

echo json_encode([
    'status' => 'success',
    'message' => 'Sepetinize Eklendi!',
    'cart_count' => $cart_count,
    'cart_total' => number_format($cart_total, 2, '.', '')
]);
