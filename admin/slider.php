<?php require_once('header.php'); ?>

<?php
// Handle slider display mode update
if(isset($_POST['update_display_mode'])) {
    $display_mode = $_POST['slider_display_mode'];
    $statement = $pdo->prepare("UPDATE tbl_settings SET slider_display_mode=? WHERE id=1");
    $statement->execute(array($display_mode));
    $success_message = 'Slider display mode updated successfully!';
}

// Get current display mode
$statement = $pdo->prepare("SELECT slider_display_mode FROM tbl_settings WHERE id=1");
$statement->execute();
$settings_row = $statement->fetch(PDO::FETCH_ASSOC);
$current_display_mode = isset($settings_row['slider_display_mode']) ? $settings_row['slider_display_mode'] : 'slider';
?>

<style>
.display-mode-toggle {
    display: flex;
    gap: 10px;
    align-items: center;
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.display-mode-toggle label {
    font-weight: 600;
    margin-right: 15px;
    color: #333;
}
.mode-btn {
    padding: 10px 20px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mode-btn:hover {
    border-color: #3c8dbc;
    background: #f0f7fc;
}
.mode-btn.active {
    border-color: #3c8dbc;
    background: #3c8dbc;
    color: white;
}
.mode-btn i {
    font-size: 16px;
}
.mode-preview {
    margin-left: auto;
    padding: 8px 15px;
    background: #e7f3ff;
    border-radius: 6px;
    color: #3c8dbc;
    font-size: 13px;
}
</style>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Sliders</h1>
	</div>
	<div class="content-header-right">
		<a href="slider-add.php" class="btn btn-primary btn-sm">Add Slider</a>
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

			<!-- Display Mode Toggle -->
			<form method="post" action="">
				<div class="display-mode-toggle">
					<label><i class="fa fa-desktop"></i> Display Mode:</label>
					<button type="submit" name="update_display_mode" class="mode-btn <?php echo ($current_display_mode == 'slider') ? 'active' : ''; ?>" onclick="document.getElementById('mode_input').value='slider';">
						<i class="fa fa-arrows-h"></i> Normal Slider
					</button>
					<button type="submit" name="update_display_mode" class="mode-btn <?php echo ($current_display_mode == 'cube') ? 'active' : ''; ?>" onclick="document.getElementById('mode_input').value='cube';">
						<i class="fa fa-cube"></i> Flipping Cubes
					</button>
					<input type="hidden" name="slider_display_mode" id="mode_input" value="<?php echo $current_display_mode; ?>">
					<div class="mode-preview">
						<i class="fa fa-eye"></i> Current: <strong><?php echo ($current_display_mode == 'cube') ? 'Flipping Cubes' : 'Normal Slider'; ?></strong>
					</div>
				</div>
			</form>

			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Photo</th>
								<th>Heading</th>
								<th>Content</th>
								<th>Button Text</th>
								<th>Button URL</th>
								<th>Position</th>
								<th width="140">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT
														
														id,
														photo,
														heading,
														content,
														button_text,
														button_url,
														position

							                           	FROM tbl_slider
							                           	
							                           	");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:150px;"><img src="../assets/uploads/<?php echo $row['photo']; ?>" alt="<?php echo $row['heading']; ?>" style="width:140px;"></td>
									<td><?php echo $row['heading']; ?></td>
									<td><?php echo $row['content']; ?></td>
									<td><?php echo $row['button_text']; ?></td>
									<td><?php echo $row['button_url']; ?></td>
									<td><?php echo $row['position']; ?></td>
									<td>										
										<a href="slider-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="slider-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
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
                <p>Are you sure want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>