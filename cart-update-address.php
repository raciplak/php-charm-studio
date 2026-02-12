<?php
session_start();
ob_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");

header('Content-Type: application/json');

if(!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$type = isset($_POST['type']) ? $_POST['type'] : '';
$cust_id = $_SESSION['customer']['cust_id'];

if($type == 'billing') {
    $name = $_POST['b_name'];
    $address = $_POST['b_address'];
    $city = $_POST['b_city'];
    $state = $_POST['b_state'];
    $zip = $_POST['b_zip'];
    $phone = $_POST['b_phone'];
    $country = $_POST['b_country'];

    $statement = $pdo->prepare("UPDATE tbl_customer SET cust_b_name=?, cust_b_address=?, cust_b_city=?, cust_b_state=?, cust_b_zip=?, cust_b_phone=?, cust_b_country=? WHERE cust_id=?");
    $statement->execute([$name, $address, $city, $state, $zip, $phone, $country, $cust_id]);

    $_SESSION['customer']['cust_b_name'] = $name;
    $_SESSION['customer']['cust_b_address'] = $address;
    $_SESSION['customer']['cust_b_city'] = $city;
    $_SESSION['customer']['cust_b_state'] = $state;
    $_SESSION['customer']['cust_b_zip'] = $zip;
    $_SESSION['customer']['cust_b_phone'] = $phone;
    $_SESSION['customer']['cust_b_country'] = $country;

    echo json_encode(['success' => true]);
} elseif($type == 'shipping') {
    $name = $_POST['s_name'];
    $address = $_POST['s_address'];
    $city = $_POST['s_city'];
    $state = $_POST['s_state'];
    $zip = $_POST['s_zip'];
    $phone = $_POST['s_phone'];
    $country = $_POST['s_country'];

    $statement = $pdo->prepare("UPDATE tbl_customer SET cust_s_name=?, cust_s_address=?, cust_s_city=?, cust_s_state=?, cust_s_zip=?, cust_s_phone=?, cust_s_country=? WHERE cust_id=?");
    $statement->execute([$name, $address, $city, $state, $zip, $phone, $country, $cust_id]);

    $_SESSION['customer']['cust_s_name'] = $name;
    $_SESSION['customer']['cust_s_address'] = $address;
    $_SESSION['customer']['cust_s_city'] = $city;
    $_SESSION['customer']['cust_s_state'] = $state;
    $_SESSION['customer']['cust_s_zip'] = $zip;
    $_SESSION['customer']['cust_s_phone'] = $phone;
    $_SESSION['customer']['cust_s_country'] = $country;

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
}
