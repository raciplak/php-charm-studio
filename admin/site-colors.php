<?php include("header.php"); ?>

<?php
$error_message = '';
$success_message = '';

// Update colors
if(isset($_POST['form_site_colors'])) {
    try {
        $csrf->verifyRequest();
        
        if(isset($_POST['color_id']) && is_array($_POST['color_id'])) {
            for($i=0; $i < count($_POST['color_id']); $i++) {
                $id = intval($_POST['color_id'][$i]);
                $value = trim($_POST['color_value'][$i]);
                // Ensure # prefix
                if(substr($value, 0, 1) !== '#') {
                    $value = '#' . $value;
                }
                // Validate hex
                if(!preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                    continue;
                }
                
                $statement = $pdo->prepare("UPDATE tbl_site_colors SET color_value=? WHERE id=?");
                $statement->execute(array($value, $id));
            }
            $success_message = 'Site renkleri başarıyla güncellendi!';
        }
    } catch(Exception $e) {
        $error_message = 'Hata: ' . $e->getMessage();
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
                                <input type="hidden" name="color_id[]" value="<?php echo intval($color['id']); ?>">
                                <input type="color" 
                                       class="color-picker-input"
                                       value="<?php echo htmlspecialchars($color['color_value']); ?>" 
                                       data-hex="hex-<?php echo $color['id']; ?>"
                                       oninput="syncFromPicker(this)"
                                       style="width:50px;height:40px;border:1px solid #ccc;border-radius:4px;padding:2px;cursor:pointer;background:transparent;">
                                <input type="text" 
                                       name="color_value[]" 
                                       id="hex-<?php echo $color['id']; ?>"
                                       value="<?php echo htmlspecialchars($color['color_value']); ?>" 
                                       data-picker="picker-<?php echo $color['id']; ?>"
                                       oninput="syncFromHex(this)"
                                       maxlength="7"
                                       style="width:100px;height:40px;border:1px solid #ccc;border-radius:4px;padding:4px 8px;font-size:14px;font-family:monospace;text-transform:uppercase;">
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
            </ul>
        </div>
    </div>

</section>

<script>
function syncFromPicker(picker) {
    var hexId = picker.getAttribute('data-hex');
    var hexInput = document.getElementById(hexId);
    if(hexInput) {
        hexInput.value = picker.value.toUpperCase();
    }
}

function syncFromHex(hexInput) {
    var val = hexInput.value.trim();
    if(/^#[0-9a-fA-F]{6}$/.test(val)) {
        // Find the corresponding color picker
        var id = hexInput.id;
        var pickers = document.querySelectorAll('input[type="color"]');
        for(var i = 0; i < pickers.length; i++) {
            if(pickers[i].getAttribute('data-hex') === id) {
                pickers[i].value = val;
                break;
            }
        }
    }
}
</script>

<?php include("footer.php"); ?>
