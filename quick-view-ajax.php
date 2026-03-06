<?php
/**
 * AJAX endpoint for Quick View - returns product data as JSON
 */

// Suppress PHP errors from breaking JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    include("admin/inc/config.php");

    if(!isset($_GET['id'])) {
        echo json_encode(['error' => 'No product ID']);
        exit;
    }

    $pid = intval($_GET['id']);

    // Get product info
    $stmt = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$product) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // Get all photos (featured + other photos)
    $photos = [];
    if(!empty($product['p_featured_photo'])) {
        $photos[] = 'assets/uploads/product_photos/' . $product['p_featured_photo'];
    }

    // Try to get additional photos
    try {
        $stmt2 = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=? ORDER BY pp_id ASC");
        $stmt2->execute([$pid]);
        $otherPhotos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach($otherPhotos as $ph) {
            if(!empty($ph['photo'])) {
                $photos[] = 'assets/uploads/product_photos/' . $ph['photo'];
            }
        }
    } catch(Exception $e) {
        // Ignore - just use featured photo
    }

    // Get currency symbol
    $currency = '$';
    try {
        $stmt3 = $pdo->prepare("SELECT * FROM tbl_language WHERE id=1");
        $stmt3->execute();
        $lang = $stmt3->fetch(PDO::FETCH_ASSOC);
        if($lang && isset($lang['lang_value'])) {
            $currency = $lang['lang_value'];
        }
    } catch(Exception $e) {
        // Use default
    }

    // Safe description
    $desc = '';
    if(!empty($product['p_description'])) {
        $stripped = strip_tags($product['p_description']);
        if(function_exists('mb_substr')) {
            $desc = mb_substr($stripped, 0, 200, 'UTF-8');
        } else {
            $desc = substr($stripped, 0, 200);
        }
        if(strlen($stripped) > 200) $desc .= '...';
    }

    echo json_encode([
        'id' => $product['p_id'],
        'name' => $product['p_name'],
        'current_price' => $product['p_current_price'],
        'old_price' => isset($product['p_old_price']) ? $product['p_old_price'] : '',
        'qty' => $product['p_qty'],
        'short_desc' => $desc,
        'photos' => $photos,
        'currency' => $currency,
        'url' => 'product.php?id=' . $product['p_id']
    ], JSON_UNESCAPED_UNICODE);

} catch(Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
