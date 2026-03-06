<?php
// Error Reporting Turn On
ini_set('error_reporting', E_ALL);

// Setting up the time zone
date_default_timezone_set('America/Los_Angeles');

// Host Name
$dbhost = 'localhost';

// Database Name
$dbname = 'merpamot_test';

// Database Username
$dbuser = 'merpamot_admin';

// Database Password
$dbpass = 'tGxOxKlFlT78';

// Defining base url
define("BASE_URL", "https://merpamotor.com.tr/eticaret/php-charm-studio-main/");

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4", $dbuser, $dbpass, array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
	));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec("SET NAMES utf8mb4");
	$pdo->exec("SET CHARACTER SET utf8mb4");
	$pdo->exec("SET collation_connection = 'utf8mb4_unicode_ci'");
}
catch( PDOException $exception ) {
	echo "Connection error :" . $exception->getMessage();
}