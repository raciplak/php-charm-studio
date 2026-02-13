<?php require_once('header.php'); ?>

<?php
// Save settings
if(isset($_POST['form_save_paratika'])) {
    $statement = $pdo->prepare("SELECT COUNT(*) FROM tbl_payment_gateway_settings WHERE gateway_name='paratika'");
    $statement->execute();
    $count = $statement->fetchColumn();

    if($count > 0) {
        $statement = $pdo->prepare("UPDATE tbl_payment_gateway_settings SET 
            api_url=?, direct_post_3d_url=?, merchant_id=?, api_username=?, api_password=?, is_active=?
            WHERE gateway_name='paratika'");
        $statement->execute(array(
            $_POST['api_url'],
            $_POST['direct_post_3d_url'],
            $_POST['merchant_id'],
            $_POST['api_username'],
            $_POST['api_password'],
            isset($_POST['is_active']) ? 1 : 0
        ));
    } else {
        $statement = $pdo->prepare("INSERT INTO tbl_payment_gateway_settings (gateway_name, api_url, direct_post_3d_url, merchant_id, api_username, api_password, is_active) VALUES ('paratika',?,?,?,?,?,?)");
        $statement->execute(array(
            $_POST['api_url'],
            $_POST['direct_post_3d_url'],
            $_POST['merchant_id'],
            $_POST['api_username'],
            $_POST['api_password'],
            isset($_POST['is_active']) ? 1 : 0
        ));
    }
    $success_message = 'Paratika ayarları başarıyla kaydedildi.';
}

// Load current settings
$paratika = array('api_url'=>'', 'direct_post_3d_url'=>'', 'merchant_id'=>'', 'api_username'=>'', 'api_password'=>'', 'is_active'=>0);
$statement = $pdo->prepare("SELECT * FROM tbl_payment_gateway_settings WHERE gateway_name='paratika' LIMIT 1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if(count($result) > 0) {
    $paratika = $result[0];
}
?>

<section class="content-header">
    <h1>Sanal Pos Ayarları (Paratika)</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">

            <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Paratika API Ayarları</h3>
                </div>
                <form action="" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label>API URL</label>
                            <input type="text" name="api_url" class="form-control" value="<?php echo htmlspecialchars($paratika['api_url']); ?>" placeholder="https://vpos.paratika.com.tr/paratika/api/v2">
                        </div>
                        <div class="form-group">
                            <label>Direct Post 3D URL</label>
                            <input type="text" name="direct_post_3d_url" class="form-control" value="<?php echo htmlspecialchars($paratika['direct_post_3d_url']); ?>" placeholder="https://vpos.paratika.com.tr/paratika/api/v2/post/sale3d/{SESSIONTOKEN}">
                            <p class="help-block">URL içinde <code>{SESSIONTOKEN}</code> yer tutucusu bulunmalıdır.</p>
                        </div>
                        <div class="form-group">
                            <label>Merchant ID</label>
                            <input type="text" name="merchant_id" class="form-control" value="<?php echo htmlspecialchars($paratika['merchant_id']); ?>" placeholder="Merchant ID">
                        </div>
                        <div class="form-group">
                            <label>API Kullanıcı Adı</label>
                            <input type="text" name="api_username" class="form-control" value="<?php echo htmlspecialchars($paratika['api_username']); ?>" placeholder="API kullanıcı adı">
                        </div>
                        <div class="form-group">
                            <label>API Şifresi</label>
                            <input type="password" name="api_password" class="form-control" value="<?php echo htmlspecialchars($paratika['api_password']); ?>" placeholder="API şifresi">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" value="1" <?php echo ($paratika['is_active'] == 1) ? 'checked' : ''; ?>>
                                Aktif (Checkout sayfasında Kredi Kartı seçeneği görünsün)
                            </label>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="form_save_paratika" class="btn btn-primary">Ayarları Kaydet</button>
                        <a href="order.php" class="btn btn-default">İşlemler Listesi</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Bilgi</h3>
                </div>
                <div class="box-body">
                    <p><strong>Callback URL (Otomatik):</strong></p>
                    <code style="word-break:break-all;"><?php echo BASE_URL; ?>payment/paratika/callback.php</code>
                    <hr>
                    <p>Callback URL, ödeme başlatılırken API'ye <code>RETURNURL</code> ve <code>ERRORURL</code> parametreleri olarak otomatik gönderilir. Paratika panelinde manuel bir ayar yapmanıza <strong>gerek yoktur</strong>.</p>
                    <hr>
                    <p><strong>Veritabanı:</strong> Önce <code>DATABASE FILE/paratika_tables.sql</code> dosyasını çalıştırarak tabloları oluşturun.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>
