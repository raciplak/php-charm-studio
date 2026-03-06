<?php
/**
 * AJAX endpoint for Quick View - returns product data as JSON
 */
header('Content-Type: application/json');
include("admin/inc/config.php");

if(!isset($_GET['id'])) {
    echo json_encode(['error' => 'No product ID']);
    exit;
}

$pid = intval($_GET['id']);

// Get product info
$stmt = $pdo->prepare("SELECT p.*, ec.ecat_name, mc.mcat_name, tc.tcat_name 
    FROM tbl_product p 
    LEFT JOIN tbl_end_category ec ON p.ecat_id = ec.ecat_id 
    LEFT JOIN tbl_mid_category mc ON ec.mcat_id = mc.mcat_id 
    LEFT JOIN tbl_top_category tc ON mc.tcat_id = tc.tcat_id 
    WHERE p.p_id=?");
$stmt->execute([$pid]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

// Get all photos (featured + other photos)
$photos = [];
$photos[] = 'assets/uploads/product_photos/' . $product['p_featured_photo'];

$stmt2 = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=? ORDER BY id ASC");
$stmt2->execute([$pid]);
$otherPhotos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach($otherPhotos as $ph) {
    $photos[] = 'assets/uploads/product_photos/' . $ph['photo'];
}

// Get currency symbol
$stmt3 = $pdo->prepare("SELECT * FROM tbl_language WHERE id=1");
$stmt3->execute();
$lang = $stmt3->fetch(PDO::FETCH_ASSOC);
$currency = $lang ? $lang['lang_value'] : '₺';

echo json_encode([
    'id' => $product['p_id'],
    'name' => $product['p_name'],
    'current_price' => $product['p_current_price'],
    'old_price' => $product['p_old_price'],
    'qty' => $product['p_qty'],
    'short_desc' => mb_substr(strip_tags($product['p_description']), 0, 200) . '...',
    'photos' => $photos,
    'currency' => $currency,
    'url' => 'product.php?id=' . $product['p_id']
]);
