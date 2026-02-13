<?php require_once('header.php'); ?>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12">
                <div style="text-align:center; padding: 40px 20px;">
                    <div style="width:80px; height:80px; margin:0 auto 20px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-times" style="font-size:40px; color:#ef4444;"></i>
                    </div>
                    <h3 style="color:#ef4444; margin-bottom:15px;">Ödeme Başarısız</h3>
                    <?php if(isset($_GET['error'])): ?>
                    <p style="color:#666; margin-bottom:5px;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
                    <?php endif; ?>
                    <?php if(isset($_GET['code']) && !empty($_GET['code'])): ?>
                    <p style="color:#999; font-size:12px; margin-bottom:20px;">Hata Kodu: <?php echo htmlspecialchars($_GET['code']); ?></p>
                    <?php endif; ?>
                    <p style="color:#666; margin-bottom:20px;">Ödeme işlemi tamamlanamadı. Lütfen tekrar deneyiniz veya farklı bir ödeme yöntemi seçiniz.</p>
                    <a href="checkout.php" class="btn btn-primary" style="margin-right:10px;">Tekrar Dene</a>
                    <a href="dashboard.php" class="btn btn-default"><?php echo LANG_VALUE_91; ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
