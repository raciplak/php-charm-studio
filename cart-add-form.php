<?php
/**
 * Simple form-based add to cart
 * Receives POST, adds to session, redirects back
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
$p_qty = 1;
$size_id = 0;
$color_id = 0;

// Get product info from DB
$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
$statement->execute(array($p_id));
$product = $statement->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header('location: ' . ($_POST['redirect'] ?? 'index.php'));
    exit;
}

$p_current_price = $product['p_current_price'];
$p_name = $product['p_name'];
$p_featured_photo = $product['p_featured_photo'];
$p_stock = $product['p_qty'];

if($p_stock < 1) {
    header('location: ' . ($_POST['redirect'] ?? 'index.php'));
    exit;
}

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

if(!$already) {
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
    $_SESSION['cart_p_qty'][$new_key] = $p_qty;
    $_SESSION['cart_p_current_price'][$new_key] = $p_current_price;
    $_SESSION['cart_p_name'][$new_key] = $p_name;
    $_SESSION['cart_p_featured_photo'][$new_key] = $p_featured_photo;
}

// Set flash message
$_SESSION['cart_flash'] = $already ? 'already' : 'added';

// Redirect back
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
header('location: ' . $redirect);
exit;
