<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_brands WHERE brand_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	// Delete all models under this brand
	$statement = $pdo->prepare("DELETE FROM tbl_models WHERE brand_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete the brand
	$statement = $pdo->prepare("DELETE FROM tbl_brands WHERE brand_id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: brand.php');
?>
