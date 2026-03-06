<?php
/**
 * Simple form-based add to cart
 * If ajax=1, returns JS to notify parent window
 * Otherwise redirects back
 */
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['p_id'])) {
    header('location: index.php');
    exit;
}

$p_id = intval($_POST['p_id']);
$is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

// Get product info from DB
$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
$statement->execute(array($p_id));
$product = $statement->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    if($is_ajax) { echo '<script>parent.cartResult("error","Ürün bulunamadı");</script>'; exit; }
    header('location: index.php');
    exit;
}

$p_current_price = $product['p_current_price'];
$p_name = $product['p_name'];
$p_featured_photo = $product['p_featured_photo'];
$p_stock = $product['p_qty'];

if($p_stock < 1) {
    if($is_ajax) { echo '<script>parent.cartResult("error","Stokta yok");</script>'; exit; }
    header('location: index.php');
    exit;
}

$size_id = 0;
$color_id = 0;
$size_name = '';
$color_name = '';

// Check if already in cart
$already = false;
if(isset($_SESSION['cart_p_id'])) {
    foreach($_SESSION['cart_p_id'] as $key => $val) {
        if($val == $p_id && $_SESSION['cart_size_id'][$key] == $size_id && $_SESSION['cart_color_id'][$key] == $color_id) {
            $already = true;
            break;
        }
    }
}

if($already) {
    if($is_ajax) { echo '<script>parent.cartResult("already","Bu ürün zaten sepetinizde");</script>'; exit; }
    header('location: ' . ($_POST['redirect'] ?? 'index.php'));
    exit;
}

// Add to cart
if(isset($_SESSION['cart_p_id']) && count($_SESSION['cart_p_id']) > 0) {
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
$_SESSION['cart_p_qty'][$new_key] = 1;
$_SESSION['cart_p_current_price'][$new_key] = $p_current_price;
$_SESSION['cart_p_name'][$new_key] = $p_name;
$_SESSION['cart_p_featured_photo'][$new_key] = $p_featured_photo;

// Calculate totals
$cart_count = count($_SESSION['cart_p_id']);
$cart_total = 0;
foreach($_SESSION['cart_p_qty'] as $k => $q) {
    $cart_total += $q * $_SESSION['cart_p_current_price'][$k];
}

if($is_ajax) {
    echo '<script>parent.cartResult("success","Sepetinize Eklendi",' . $cart_count . ',"' . number_format($cart_total, 2, '.', '') . '");</script>';
    exit;
}

$_SESSION['cart_flash'] = 'added';
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
header('location: ' . $redirect);
exit;
