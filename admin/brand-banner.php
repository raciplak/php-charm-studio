<?php require_once('header.php'); ?>

<?php
$success_message = '';
if(isset($_GET['status_changed'])) {
    $success_message = 'Brand banner status updated successfully!';
}
?>

<style>
.inactive-row {
    background-color: #f9f9f9 !important;
    opacity: 0.7;
}
.inactive-row td {
    color: #999;
}
</style>

<section class="content-header">
	<div class="content-header-left">
		<h1>Marka Bannerları</h1>
	</div>
	<div class="content-header-right">
		<a href="brand-banner-add.php" class="btn btn-primary btn-sm">Yeni Ekle</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">

			<?php if($success_message): ?>
			<div class="callout callout-success">
				<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Fotoğraf</th>
								<th>Başlık</th>
								<th>Alt Başlık</th>
								<th>Buton Metni</th>
								<th>Buton URL</th>
								<th>Sıra</th>
								<th>Durum</th>
								<th width="140">İşlem</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT * FROM tbl_brand_banner ORDER BY display_order ASC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
							foreach ($result as $row) {
								$i++;
								$is_active = isset($row['is_active']) ? $row['is_active'] : 1;
								?>
								<tr class="<?php echo ($is_active == 0) ? 'inactive-row' : ''; ?>">
									<td><?php echo $i; ?></td>
									<td style="width:150px;"><img src="../assets/uploads/<?php echo $row['photo']; ?>" alt="<?php echo $row['title']; ?>" style="width:140px; <?php echo ($is_active == 0) ? 'opacity:0.5;' : ''; ?>"></td>
									<td><?php echo $row['title']; ?></td>
									<td><?php echo $row['subtitle']; ?></td>
									<td><?php echo $row['button_text']; ?></td>
									<td><?php echo $row['button_url']; ?></td>
									<td><?php echo $row['display_order']; ?></td>
									<td>
										<?php if($is_active == 1): ?>
											<a href="brand-banner-change-status.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-xs" title="Pasif yap">
												<i class="fa fa-check"></i> Aktif
											</a>
										<?php else: ?>
											<a href="brand-banner-change-status.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-xs" title="Aktif yap">
												<i class="fa fa-times"></i> Pasif
											</a>
										<?php endif; ?>
									</td>
									<td>										
										<a href="brand-banner-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Düzenle</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="brand-banner-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Sil</a>  
									</td>
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Silme Onayı</h4>
            </div>
            <div class="modal-body">
                <p>Bu marka bannerını silmek istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <a class="btn btn-danger btn-ok">Sil</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
