<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['color_name'])) {
        $valid = 0;
        $error_message .= "Renk Adı boş olamaz<br>";
    } else {
    	$statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row) {
			$current_color_name = $row['color_name'];
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_name=? and color_name!=?");
    	$statement->execute(array($_POST['color_name'],$current_color_name));
    	$total = $statement->rowCount();							
    	if($total) {
    		$valid = 0;
        	$error_message .= 'Renk adı zaten mevcut<br>';
    	}
    }

    $color_code = isset($_POST['color_code']) ? $_POST['color_code'] : '#000000';

    if($valid == 1) {    	
		$statement = $pdo->prepare("UPDATE tbl_color SET color_name=?, color_code=? WHERE color_id=?");
		$statement->execute(array($_POST['color_name'], $color_code, $_REQUEST['id']));

    	$success_message = 'Renk başarıyla güncellendi.';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
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
		<h1>Renk Düzenle</h1>
	</div>
	<div class="content-header-right">
		<a href="color.php" class="btn btn-primary btn-sm">Tümünü Görüntüle</a>
	</div>
</section>

<?php							
foreach ($result as $row) {
	$color_name = $row['color_name'];
	$color_code = isset($row['color_code']) ? $row['color_code'] : '#000000';
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

        <form class="form-horizontal" action="" method="post" accept-charset="UTF-8">

        <div class="box box-info">

            <div class="box-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Renk Adı <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="color_name" value="<?php echo $color_name; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Renk Kodu</label>
                    <div class="col-sm-4">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="color" name="color_code" value="<?php echo $color_code; ?>" style="width:60px; height:38px; padding:2px; cursor:pointer; border:1px solid #ccc; border-radius:4px;" id="colorPicker">
                            <input type="text" class="form-control" id="colorHex" value="<?php echo $color_code; ?>" style="width:120px; font-family:monospace;" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                	<label for="" class="col-sm-2 control-label"></label>
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-success pull-left" name="form1">Güncelle</button>
                    </div>
                </div>

            </div>

        </div>

        </form>

    </div>
  </div>

</section>

<script>
document.getElementById('colorPicker').addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});
</script>

<?php require_once('footer.php'); ?>
