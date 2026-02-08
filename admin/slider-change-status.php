<?php
require_once('inc/config.php');
require_once('inc/functions.php');

if(!isset($_SESSION['admin'])) {
    header('location: login.php');
    exit;
}

if(!isset($_REQUEST['id'])) {
    header('location: slider.php');
    exit;
}

$id = $_REQUEST['id'];

// Get current status
$statement = $pdo->prepare("SELECT is_active FROM tbl_slider WHERE id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);

// Toggle status
$new_status = ($row['is_active'] == 1) ? 0 : 1;

// Update status
$statement = $pdo->prepare("UPDATE tbl_slider SET is_active=? WHERE id=?");
$statement->execute(array($new_status, $id));

header('location: slider.php');
exit;
