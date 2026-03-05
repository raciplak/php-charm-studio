<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_brand_banner WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_brand_banner WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$photo = $row['photo'];
	}

	if($photo!='') {
		unlink('../assets/uploads/'.$photo);	
	}

	$statement = $pdo->prepare("DELETE FROM tbl_brand_banner WHERE id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: brand-banner.php');
?>
