<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['brand_id'])) {
        $valid = 0;
        $error_message .= "You must select a Brand<br>";
    }

    if(empty($_POST['model_code'])) {
        $valid = 0;
        $error_message .= "Model Code can not be empty<br>";
    }

    if(empty($_POST['model_name'])) {
        $valid = 0;
        $error_message .= "Model Name can not be empty<br>";
    }

    if($valid == 1) {    	
		$statement = $pdo->prepare("UPDATE tbl_models SET model_code=?, model_name=?, brand_id=?, show_on_menu=? WHERE model_id=?");
		$statement->execute(array($_POST['model_code'], $_POST['model_name'], $_POST['brand_id'], $_POST['show_on_menu'], $_REQUEST['id']));

    	$success_message = 'Model is updated successfully.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_models WHERE model_id=?");
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
		<h1>Edit Model</h1>
	</div>
	<div class="content-header-right">
		<a href="model.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<?php							
foreach ($result as $row) {
	$model_code = $row['model_code'];
	$model_name = $row['model_name'];
	$brand_id = $row['brand_id'];
	$show_on_menu = $row['show_on_menu'];
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
                    <label for="" class="col-sm-3 control-label">Brand <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="brand_id" class="form-control select2">
                            <option value="">Select Brand</option>
                            <?php
                            $statement = $pdo->prepare("SELECT * FROM tbl_brands ORDER BY brand_name ASC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);   
                            foreach ($result as $row) {
                                ?>
                                <option value="<?php echo $row['brand_id']; ?>" <?php if($row['brand_id'] == $brand_id){echo 'selected';} ?>><?php echo $row['brand_name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Model Code <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="model_code" value="<?php echo $model_code; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Model Name <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="model_name" value="<?php echo $model_name; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Menüde Göster? <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="show_on_menu" class="form-control" style="width:auto;">
                            <option value="0" <?php if($show_on_menu == 0) {echo 'selected';} ?>>Hayır</option>
                            <option value="1" <?php if($show_on_menu == 1) {echo 'selected';} ?>>Evet</option>
                        </select>
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
