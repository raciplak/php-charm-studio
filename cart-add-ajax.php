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
$p_qty = isset($_POST['p_qty']) ? intval($_POST['p_qty']) : 1;
$size_id = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;
$color_id = isset($_POST['color_id']) ? intval($_POST['color_id']) : 0;
$p_current_price = isset($_POST['p_current_price']) ? $_POST['p_current_price'] : 0;
$p_name = isset($_POST['p_name']) ? $_POST['p_name'] : '';
$p_featured_photo = isset($_POST['p_featured_photo']) ? $_POST['p_featured_photo'] : '';

if($p_id == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz ürün']);
    exit;
}

// Check stock
$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
$statement->execute(array($p_id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if(count($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ürün bulunamadı']);
    exit;
}
$current_p_qty = $result[0]['p_qty'];

if($p_qty > $current_p_qty) {
    echo json_encode(['status' => 'error', 'message' => 'Stokta sadece '.$current_p_qty.' adet var']);
    exit;
}

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
    $arr_p_id = array_values($_SESSION['cart_p_id']);
    $arr_size = array_values($_SESSION['cart_size_id']);
    $arr_color = array_values($_SESSION['cart_color_id']);
    
    for($i=0; $i<count($arr_p_id); $i++) {
        if($arr_p_id[$i] == $p_id && $arr_size[$i] == $size_id && $arr_color[$i] == $color_id) {
            echo json_encode(['status' => 'error', 'message' => 'Bu ürün zaten sepetinizde']);
            exit;
        }
    }
    
    // Add to existing cart
    $keys = array_keys($_SESSION['cart_p_id']);
    $new_key = max($keys) + 1;
    
    $_SESSION['cart_p_id'][$new_key] = $p_id;
    $_SESSION['cart_size_id'][$new_key] = $size_id;
    $_SESSION['cart_size_name'][$new_key] = $size_name;
    $_SESSION['cart_color_id'][$new_key] = $color_id;
    $_SESSION['cart_color_name'][$new_key] = $color_name;
    $_SESSION['cart_p_qty'][$new_key] = $p_qty;
    $_SESSION['cart_p_current_price'][$new_key] = $p_current_price;
    $_SESSION['cart_p_name'][$new_key] = $p_name;
    $_SESSION['cart_p_featured_photo'][$new_key] = $p_featured_photo;
} else {
    $_SESSION['cart_p_id'][1] = $p_id;
    $_SESSION['cart_size_id'][1] = $size_id;
    $_SESSION['cart_size_name'][1] = $size_name;
    $_SESSION['cart_color_id'][1] = $color_id;
    $_SESSION['cart_color_name'][1] = $color_name;
    $_SESSION['cart_p_qty'][1] = $p_qty;
    $_SESSION['cart_p_current_price'][1] = $p_current_price;
    $_SESSION['cart_p_name'][1] = $p_name;
    $_SESSION['cart_p_featured_photo'][1] = $p_featured_photo;
}

// Calculate new totals
$cart_count = count($_SESSION['cart_p_id']);
$cart_total = 0;
$qtys = array_values($_SESSION['cart_p_qty']);
$prices = array_values($_SESSION['cart_p_current_price']);
for($i=0; $i<count($qtys); $i++) {
    $cart_total += $qtys[$i] * $prices[$i];
}

echo json_encode([
    'status' => 'success',
    'message' => 'Ürün sepete eklendi!',
    'cart_count' => $cart_count,
    'cart_total' => number_format($cart_total, 2, '.', '')
]);
