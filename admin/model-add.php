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
		$statement = $pdo->prepare("INSERT INTO tbl_models (model_code, model_name, brand_id, show_on_menu) VALUES (?,?,?,?)");
		$statement->execute(array($_POST['model_code'], $_POST['model_name'], $_POST['brand_id'], $_POST['show_on_menu']));
	
    	$success_message = 'Model is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Model</h1>
	</div>
	<div class="content-header-right">
		<a href="model.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


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
										<option value="<?php echo $row['brand_id']; ?>"><?php echo $row['brand_name']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Model Code <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="model_code">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Model Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="model_name">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Menüde Göster? <span>*</span></label>
							<div class="col-sm-4">
								<select name="show_on_menu" class="form-control" style="width:auto;">
									<option value="0">Hayır</option>
									<option value="1">Evet</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>

			</form>

		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>
