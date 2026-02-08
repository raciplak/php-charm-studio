<?php
ob_start();
session_start();
require_once('inc/config.php');
require_once('inc/functions.php');

if(!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

if(!isset($_REQUEST['id'])) {
    header('location: category-banner.php');
    exit;
}

// Get current status
$statement = $pdo->prepare("SELECT is_active FROM tbl_category_banner WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetch(PDO::FETCH_ASSOC);

if($result) {
    // Toggle the status
    $new_status = ($result['is_active'] == 1) ? 0 : 1;
    
    $statement = $pdo->prepare("UPDATE tbl_category_banner SET is_active=? WHERE id=?");
    $statement->execute(array($new_status, $_REQUEST['id']));
}

header('location: category-banner.php?status_changed=1');
exit;
?>
