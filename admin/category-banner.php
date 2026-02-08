<?php require_once('header.php'); ?>

<?php
$success_message = '';
if(isset($_GET['status_changed'])) {
    $success_message = 'Category banner status updated successfully!';
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
		<h1>View Category Banners</h1>
	</div>
	<div class="content-header-right">
		<a href="category-banner-add.php" class="btn btn-primary btn-sm">Add Category Banner</a>
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
								<th>Photo</th>
								<th>Title</th>
								<th>Subtitle</th>
								<th>Button Text</th>
								<th>Button URL</th>
								<th>Order</th>
								<th>Status</th>
								<th width="140">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT * FROM tbl_category_banner ORDER BY display_order ASC");
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
											<a href="category-banner-change-status.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-xs" title="Click to deactivate">
												<i class="fa fa-check"></i> Active
											</a>
										<?php else: ?>
											<a href="category-banner-change-status.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-xs" title="Click to activate">
												<i class="fa fa-times"></i> Inactive
											</a>
										<?php endif; ?>
									</td>
									<td>										
										<a href="category-banner-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="category-banner-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
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
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this category banner?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
