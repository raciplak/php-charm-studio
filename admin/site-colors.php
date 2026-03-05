<?php include("header.php"); ?>

<?php
// Update colors
if(isset($_POST['form_site_colors'])) {
    $csrf->check_valid('error');
    
    if(isset($_POST['color_id']) && is_array($_POST['color_id'])) {
        for($i=0; $i < count($_POST['color_id']); $i++) {
            $id = $_POST['color_id'][$i];
            $value = '#' . ltrim($_POST['color_value'][$i], '#');
            
            $statement = $pdo->prepare("UPDATE tbl_site_colors SET color_value=? WHERE id=?");
            $statement->execute(array($value, $id));
        }
        $success_message = 'Site renkleri başarıyla güncellendi!';
    }
}

// Fetch all colors grouped
$statement = $pdo->prepare("SELECT * FROM tbl_site_colors ORDER BY display_order ASC, id ASC");
$statement->execute();
$all_colors = $statement->fetchAll(PDO::FETCH_ASSOC);

// Group by color_group
$groups = array();
foreach($all_colors as $color) {
    $groups[$color['color_group']][] = $color;
}
?>

<section class="content-header">
    <h1>Site Renk Ayarları <small>Tüm site renklerini buradan yönetin</small></h1>
</section>

<section class="content">

    <?php if($error_message != ''): ?>
    <div class="callout callout-danger"><p><?php echo $error_message; ?></p></div>
    <?php endif; ?>
    <?php if($success_message != ''): ?>
    <div class="callout callout-success"><p><?php echo $success_message; ?></p></div>
    <?php endif; ?>

    <form action="" method="post">
        <?php $csrf->echoInputField(); ?>

        <?php foreach($groups as $group_name => $colors): ?>
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-paint-brush"></i> <?php echo htmlspecialchars($group_name); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php foreach($colors as $color): ?>
                    <div class="col-md-4 col-sm-6" style="margin-bottom:20px;">
                        <div style="border:1px solid #e0e0e0;border-radius:6px;padding:14px;background:#fafafa;">
                            <label style="font-weight:600;font-size:13px;margin-bottom:8px;display:block;">
                                <?php echo htmlspecialchars($color['color_label']); ?>
                            </label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="hidden" name="color_id[]" value="<?php echo $color['id']; ?>">
                                <input class="jscolor {hash:true, borderColor:'#ccc', backgroundColor:'#fff', padding:8, borderRadius:4}" 
                                       name="color_value[]" 
                                       value="<?php echo htmlspecialchars(ltrim($color['color_value'], '#')); ?>" 
                                       style="width:120px;height:36px;border:1px solid #ccc;border-radius:4px;padding:4px 8px;font-size:14px;font-family:monospace;">
                                <div style="width:36px;height:36px;border-radius:4px;border:1px solid #ccc;background:<?php echo htmlspecialchars($color['color_value']); ?>;flex-shrink:0;"></div>
                            </div>
                            <div style="margin-top:6px;font-size:11px;color:#888;">
                                <code style="background:#eee;padding:2px 6px;border-radius:3px;">--<?php echo htmlspecialchars($color['color_key']); ?></code>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="box box-success">
            <div class="box-body" style="text-align:center;padding:20px;">
                <button type="submit" class="btn btn-primary btn-lg" name="form_site_colors">
                    <i class="fa fa-save"></i> Tüm Renkleri Kaydet
                </button>
            </div>
        </div>

    </form>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Bilgi</h3>
        </div>
        <div class="box-body">
            <p>Bu sayfadan sitenizin tüm renklerini merkezi olarak yönetebilirsiniz.</p>
            <ul>
                <li>Renk kutucuğuna tıklayarak renk seçici açılır.</li>
                <li>Değişiklikler kaydedildikten sonra tüm sitede anında geçerli olur.</li>
                <li>Gelecekte eklenen yeni renkli alanlar da bu merkezden beslenecektir.</li>
            </ul>
        </div>
    </div>

</section>

<script src="js/jscolor.js"></script>

<?php include("footer.php"); ?>
