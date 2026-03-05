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
    header('location: brand-banner.php');
    exit;
}

$statement = $pdo->prepare("SELECT is_active FROM tbl_brand_banner WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetch(PDO::FETCH_ASSOC);

if($result) {
    $new_status = ($result['is_active'] == 1) ? 0 : 1;
    
    $statement = $pdo->prepare("UPDATE tbl_brand_banner SET is_active=? WHERE id=?");
    $statement->execute(array($new_status, $_REQUEST['id']));
}

header('location: brand-banner.php?status_changed=1');
exit;
?>
