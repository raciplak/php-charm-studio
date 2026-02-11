<?php
session_start();
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$size = isset($_GET['size']) ? $_GET['size'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';

if(empty($action) || empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

if(!isset($_SESSION['cart_p_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sepet boş']);
    exit;
}

// Find the item index
$target_index = -1;
$i = 0;
foreach($_SESSION['cart_p_id'] as $key => $value) {
    $i++;
    if($value == $id) {
        // Check size and color match
        $si = 0;
        foreach($_SESSION['cart_size_id'] as $sk => $sv) { $si++; if($si == $i) { $found_size = $sv; break; } }
        $ci = 0;
        foreach($_SESSION['cart_color_id'] as $ck => $cv) { $ci++; if($ci == $i) { $found_color = $cv; break; } }
        
        if($found_size == $size && $found_color == $color) {
            $target_index = $i;
            break;
        }
    }
}

if($target_index == -1) {
    echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
    exit;
}

if($action == 'plus') {
    $_SESSION['cart_p_qty'][$target_index] = $_SESSION['cart_p_qty'][$target_index] + 1;
} elseif($action == 'minus') {
    if($_SESSION['cart_p_qty'][$target_index] > 1) {
        $_SESSION['cart_p_qty'][$target_index] = $_SESSION['cart_p_qty'][$target_index] - 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'Miktar 1\'den az olamaz']);
        exit;
    }
} elseif($action == 'remove') {
    // Remove item from all session arrays
    $arrays = ['cart_p_id','cart_p_name','cart_p_qty','cart_p_current_price','cart_p_featured_photo','cart_size_id','cart_size_name','cart_color_id','cart_color_name'];
    foreach($arrays as $arr) {
        $new = [];
        $j = 0;
        foreach($_SESSION[$arr] as $k => $v) {
            $j++;
            if($j != $target_index) {
                $new[] = $v;
            }
        }
        // Re-index starting from 1
        $_SESSION[$arr] = [];
        foreach($new as $idx => $val) {
            $_SESSION[$arr][$idx + 1] = $val;
        }
    }
    if(count($_SESSION['cart_p_id']) == 0) {
        unset($_SESSION['cart_p_id']);
    }
}

// Build response with updated cart data
$cart_items = [];
$cart_total = 0;
$cart_count = isset($_SESSION['cart_p_id']) ? count($_SESSION['cart_p_id']) : 0;

if($cart_count > 0) {
    $sc_p_id = array_values_reindex($_SESSION['cart_p_id']);
    $sc_p_name = array_values_reindex($_SESSION['cart_p_name']);
    $sc_p_photo = array_values_reindex($_SESSION['cart_p_featured_photo']);
    $sc_p_qty = array_values_reindex($_SESSION['cart_p_qty']);
    $sc_p_price = array_values_reindex($_SESSION['cart_p_current_price']);
    $sc_size_id = array_values_reindex($_SESSION['cart_size_id']);
    $sc_size_name = array_values_reindex($_SESSION['cart_size_name']);
    $sc_color_id = array_values_reindex($_SESSION['cart_color_id']);
    $sc_color_name = array_values_reindex($_SESSION['cart_color_name']);

    for($i = 0; $i < $cart_count; $i++) {
        $row_total = $sc_p_price[$i] * $sc_p_qty[$i];
        $cart_total += $row_total;
        $cart_items[] = [
            'id' => $sc_p_id[$i],
            'name' => $sc_p_name[$i],
            'photo' => $sc_p_photo[$i],
            'qty' => $sc_p_qty[$i],
            'price' => $sc_p_price[$i],
            'row_total' => $row_total,
            'size_id' => $sc_size_id[$i],
            'size_name' => $sc_size_name[$i],
            'color_id' => $sc_color_id[$i],
            'color_name' => $sc_color_name[$i],
        ];
    }
}

echo json_encode([
    'success' => true,
    'cart_count' => $cart_count,
    'cart_total' => $cart_total,
    'items' => $cart_items
]);

function array_values_reindex($arr) {
    $result = [];
    foreach($arr as $v) {
        $result[] = $v;
    }
    return $result;
}
