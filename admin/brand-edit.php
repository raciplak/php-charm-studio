<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['brand_code'])) {
        $valid = 0;
        $error_message .= "Brand Code can not be empty<br>";
    }

    if(empty($_POST['brand_name'])) {
        $valid = 0;
        $error_message .= "Brand Name can not be empty<br>";
    } else {
    	$statement = $pdo->prepare("SELECT * FROM tbl_brands WHERE brand_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row) {
			$current_brand_name = $row['brand_name'];
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_brands WHERE brand_name=? and brand_name!=?");
    	$statement->execute(array($_POST['brand_name'], $current_brand_name));
    	$total = $statement->rowCount();							
    	if($total) {
    		$valid = 0;
        	$error_message .= 'Brand Name already exists<br>';
    	}
    }

    if($valid == 1) {    	
		$statement = $pdo->prepare("UPDATE tbl_brands SET brand_code=?, brand_name=? WHERE brand_id=?");
		$statement->execute(array($_POST['brand_code'], $_POST['brand_name'], $_REQUEST['id']));

    	$success_message = 'Brand is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_brands WHERE brand_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Brand</h1>
	</div>
	<div class="content-header-right">
		<a href="brand.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<?php							
foreach ($result as $row) {
	$brand_code = $row['brand_code'];
	$brand_name = $row['brand_name'];
}
?>

<section class="content">

  <div class="row">
    <div class="col-md-12">

		<?php if($error_message): ?>
		<div class="callout callout-danger">
		<p><?php echo $error_message; ?></p>
		</div>
		<?php endif; ?>

		<?php if($success_message): ?>
		<div class="callout callout-success">
		<p><?php echo $success_message; ?></p>
		</div>
		<?php endif; ?>

        <form class="form-horizontal" action="" method="post">

        <div class="box box-info">

            <div class="box-body">
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Brand Code <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="brand_code" value="<?php echo $brand_code; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Brand Name <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="brand_name" value="<?php echo $brand_name; ?>">
                    </div>
                </div>
                <div class="form-group">
                	<label for="" class="col-sm-3 control-label"></label>
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                    </div>
                </div>
            </div>

        </div>

        </form>

    </div>
  </div>

</section>

<?php require_once('footer.php'); ?>
