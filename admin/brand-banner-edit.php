<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'jpg, jpeg, gif veya png dosyası yüklemelisiniz<br>';
        }
    }

	if($valid == 1) {

		if($path == '') {
			$statement = $pdo->prepare("UPDATE tbl_brand_banner SET title=?, subtitle=?, button_text=?, button_url=?, display_order=? WHERE id=?");
    		$statement->execute(array($_POST['title'],$_POST['subtitle'],$_POST['button_text'],$_POST['button_url'],$_POST['display_order'],$_REQUEST['id']));
		} else {

			unlink('../assets/uploads/'.$_POST['current_photo']);

			$final_name = 'brand-banner-'.$_REQUEST['id'].'.'.$ext;
        	move_uploaded_file( $path_tmp, '../assets/uploads/'.$final_name );

        	$statement = $pdo->prepare("UPDATE tbl_brand_banner SET photo=?, title=?, subtitle=?, button_text=?, button_url=?, display_order=? WHERE id=?");
    		$statement->execute(array($final_name,$_POST['title'],$_POST['subtitle'],$_POST['button_text'],$_POST['button_url'],$_POST['display_order'],$_REQUEST['id']));
		}	   

	    $success_message = 'Marka Bannerı başarıyla güncellendi!';
	}
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	$statement = $pdo->prepare("SELECT * FROM tbl_brand_banner WHERE id=?");
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
		<h1>Marka Bannerı Düzenle</h1>
	</div>
	<div class="content-header-right">
		<a href="brand-banner.php" class="btn btn-primary btn-sm">Tümünü Görüntüle</a>
	</div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_brand_banner WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$photo        = $row['photo'];
	$title        = $row['title'];
	$subtitle     = $row['subtitle'];
	$button_text  = $row['button_text'];
	$button_url   = $row['button_url'];
	$display_order = $row['display_order'];
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

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="current_photo" value="<?php echo $photo; ?>">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Mevcut Fotoğraf</label>
							<div class="col-sm-9" style="padding-top:5px">
								<img src="../assets/uploads/<?php echo $photo; ?>" alt="Marka Banner Fotoğrafı" style="width:400px;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Fotoğraf </label>
							<div class="col-sm-6" style="padding-top:5px">
								<input type="file" name="photo">(Sadece jpg, jpeg, gif ve png)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Başlık </label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="title" value="<?php echo $title; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Alt Başlık </label>
							<div class="col-sm-6">
								<textarea class="form-control" name="subtitle" style="height:100px;"><?php echo $subtitle; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Buton Metni </label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="button_text" value="<?php echo $button_text; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Buton URL </label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="button_url" value="<?php echo $button_url; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Görüntüleme Sırası </label>
							<div class="col-sm-2">
								<input type="number" autocomplete="off" class="form-control" name="display_order" value="<?php echo $display_order; ?>">
							</div>
						</div>				
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Kaydet</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>
