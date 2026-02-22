<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$footer_about = $row['footer_about'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$contact_address = $row['contact_address'];
	$footer_copyright = $row['footer_copyright'];
	$total_recent_post_footer = $row['total_recent_post_footer'];
    $total_popular_post_footer = $row['total_popular_post_footer'];
    $newsletter_on_off = $row['newsletter_on_off'];
    $before_body = $row['before_body'];
}
?>


<?php if($newsletter_on_off == 1): ?>
<section class="home-newsletter">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="single">
					<?php
			if(isset($_POST['form_subscribe']))
			{

				if(empty($_POST['email_subscribe'])) 
			    {
			        $valid = 0;
			        $error_message1 .= LANG_VALUE_131;
			    }
			    else
			    {
			    	if (filter_var($_POST['email_subscribe'], FILTER_VALIDATE_EMAIL) === false)
				    {
				        $valid = 0;
				        $error_message1 .= LANG_VALUE_134;
				    }
				    else
				    {
				    	$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email=?");
				    	$statement->execute(array($_POST['email_subscribe']));
				    	$total = $statement->rowCount();							
				    	if($total)
				    	{
				    		$valid = 0;
				        	$error_message1 .= LANG_VALUE_147;
				    	}
				    	else
				    	{
				    		// Sending email to the requested subscriber for email confirmation
				    		// Getting activation key to send via email. also it will be saved to database until user click on the activation link.
				    		$key = md5(uniqid(rand(), true));

				    		// Getting current date
				    		$current_date = date('Y-m-d');

				    		// Getting current date and time
				    		$current_date_time = date('Y-m-d H:i:s');

				    		// Inserting data into the database
				    		$statement = $pdo->prepare("INSERT INTO tbl_subscriber (subs_email,subs_date,subs_date_time,subs_hash,subs_active) VALUES (?,?,?,?,?)");
				    		$statement->execute(array($_POST['email_subscribe'],$current_date,$current_date_time,$key,0));

				    		// Sending Confirmation Email
				    		$to = $_POST['email_subscribe'];
							$subject = 'Bülten Aboneliği E-posta Onayı';
							
							// Getting the url of the verification link
							$verification_url = BASE_URL.'verify.php?email='.$to.'&key='.$key;

							$message = '
Bültenimize abone olmak istediğiniz için teşekkür ederiz!<br><br>
Aboneliğinizi onaylamak için lütfen bu bağlantıya tıklayın:
					'.$verification_url.'<br><br>
Bu bağlantı yalnızca 24 saat geçerli olacaktır.
					';

							$headers = 'From: ' . $contact_email . "\r\n" .
								   'Reply-To: ' . $contact_email . "\r\n" .
								   'X-Mailer: PHP/' . phpversion() . "\r\n" . 
								   "MIME-Version: 1.0\r\n" . 
								   "Content-Type: text/html; charset=UTF-8\r\n";

							// Sending the email
							mail($to, $subject, $message, $headers);

							$success_message1 = LANG_VALUE_136;
				    	}
				    }
			    }
			}
			if($error_message1 != '') {
				echo "<script>alert('".$error_message1."')</script>";
			}
			if($success_message1 != '') {
				echo "<script>alert('".$success_message1."')</script>";
			}
			?>
				<form action="" method="post">
					<?php $csrf->echoInputField(); ?>
					<h2><?php echo LANG_VALUE_93; ?></h2>
					<div class="input-group">
			        	<input type="email" class="form-control" placeholder="<?php echo LANG_VALUE_95; ?>" name="email_subscribe">
			         	<span class="input-group-btn">
			         	<button class="btn btn-theme" type="submit" name="form_subscribe"><?php echo LANG_VALUE_92; ?></button>
			         	</span>
			        </div>
				</div>
				</form>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>




<div class="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12 copyright">
				<?php echo $footer_copyright; ?>
			</div>
		</div>
	</div>
</div>


<a href="#" class="scrollup">
	<i class="fa fa-angle-up"></i>
</a>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $stripe_public_key = $row['stripe_public_key'];
    $stripe_secret_key = $row['stripe_secret_key'];
}
?>

<script src="assets/js/jquery-2.2.4.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/owl.animate.js"></script>
<script src="assets/js/jquery.bxslider.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/rating.js"></script>
<script src="assets/js/jquery.touchSwipe.min.js"></script>
<script src="assets/js/bootstrap-touch-slider.js"></script>
<script src="assets/js/select2.full.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
	function confirmDelete()
	{
	    return confirm("Bu veriyi silmek istediğinizden emin misiniz?");
	}
	$(document).ready(function () {
		advFieldsStatus = $('#advFieldsStatus').val();

	$('#bank_form').hide();
		$('#kredi_karti_form').hide();

        $('#advFieldsStatus').on('change',function() {
            advFieldsStatus = $('#advFieldsStatus').val();
            if ( advFieldsStatus == '' ) {
				$('#bank_form').hide();
				$('#kredi_karti_form').hide();
            } else if ( advFieldsStatus == 'Banka Havalesi' ) {
				$('#bank_form').show();
				$('#kredi_karti_form').hide();
            } else if ( advFieldsStatus == 'Kredi Kartı' ) {
				$('#bank_form').hide();
				$('#kredi_karti_form').show();
            }
        });
	});


	$(document).on('submit', '#stripe_form', function () {
        // createToken returns immediately - the supplied callback submits the form if there are no errors
        $('#submit-button').prop("disabled", true);
        $("#msg-container").hide();
        Stripe.card.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
            // name: $('.card-holder-name').val()
        }, stripeResponseHandler);
        return false;
    });
    Stripe.setPublishableKey('<?php echo $stripe_public_key; ?>');
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('#submit-button').prop("disabled", false);
            $("#msg-container").html('<div style="color: red;border: 1px solid;margin: 10px 0px;padding: 5px;"><strong>Hata:</strong> ' + response.error.message + '</div>');
            $("#msg-container").show();
        } else {
            var form$ = $("#stripe_form");
            var token = response['id'];
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            form$.get(0).submit();
        }
    }
</script>
<!-- Side Cart Overlay -->
<div class="side-cart-overlay" id="sideCartOverlay" onclick="toggleSideCart()"></div>

<!-- Side Cart Panel -->
<div class="side-cart" id="sideCart">
    <div class="side-cart-header">
        <h3><i class="fa fa-shopping-cart"></i> Sepetim 
            <?php 
            $sc_count = isset($_SESSION['cart_p_id']) ? count($_SESSION['cart_p_id']) : 0;
            ?>
            <span class="cart-item-count"><?php echo $sc_count; ?> Ürün</span>
        </h3>
        <button class="side-cart-close" onclick="toggleSideCart()" aria-label="Kapat">&times;</button>
    </div>

    <?php if($sc_count > 0): ?>
    <div class="side-cart-shipping-bar">
        <i class="fa fa-truck"></i> Hızlı ve güvenli teslimat
    </div>
    <?php endif; ?>

    <div class="side-cart-items">
        <?php if(!isset($_SESSION['cart_p_id']) || $sc_count == 0): ?>
            <div class="side-cart-empty">
                <i class="fa fa-shopping-basket"></i>
                <h4>Sepetiniz Boş</h4>
                <p>Henüz sepetinize ürün eklemediniz.</p>
                <a href="index.php" class="btn-continue" onclick="toggleSideCart()">Alışverişe Başla</a>
            </div>
        <?php else: ?>
            <?php
            $sc_total = 0;
            $sc_i = 0;
            foreach($_SESSION['cart_p_id'] as $key => $value) { $sc_i++; $sc_p_id[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_p_name'] as $key => $value) { $sc_i++; $sc_p_name[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_p_featured_photo'] as $key => $value) { $sc_i++; $sc_p_photo[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_p_qty'] as $key => $value) { $sc_i++; $sc_p_qty[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_p_current_price'] as $key => $value) { $sc_i++; $sc_p_price[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_size_id'] as $key => $value) { $sc_i++; $sc_size_id[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_size_name'] as $key => $value) { $sc_i++; $sc_size_name[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_color_id'] as $key => $value) { $sc_i++; $sc_color_id[$sc_i] = $value; }
            $sc_i = 0;
            foreach($_SESSION['cart_color_name'] as $key => $value) { $sc_i++; $sc_color_name[$sc_i] = $value; }
            ?>

            <?php for($sc_i=1; $sc_i<=$sc_count; $sc_i++): ?>
                <?php 
                $sc_row_total = $sc_p_price[$sc_i] * $sc_p_qty[$sc_i];
                $sc_total += $sc_row_total;
                // Get original price for discount display
                $stmt_orig = $pdo->prepare("SELECT p_old_price FROM tbl_product WHERE p_id=?");
                $stmt_orig->execute(array($sc_p_id[$sc_i]));
                $orig_row = $stmt_orig->fetch(PDO::FETCH_ASSOC);
                $sc_old_price = ($orig_row && $orig_row['p_old_price'] > 0 && $orig_row['p_old_price'] > $sc_p_price[$sc_i]) ? $orig_row['p_old_price'] : 0;
                ?>
                <div class="side-cart-item" data-id="<?php echo $sc_p_id[$sc_i]; ?>" data-size="<?php echo $sc_size_id[$sc_i]; ?>" data-color="<?php echo $sc_color_id[$sc_i]; ?>" style="animation-delay: <?php echo ($sc_i - 1) * 0.05; ?>s">
                    <div class="side-cart-item-img">
                        <img src="assets/uploads/<?php echo $sc_p_photo[$sc_i]; ?>" alt="<?php echo $sc_p_name[$sc_i]; ?>">
                    </div>
                    <div class="side-cart-item-info">
                        <p class="side-cart-item-name" title="<?php echo $sc_p_name[$sc_i]; ?>"><?php echo $sc_p_name[$sc_i]; ?></p>
                        <div class="side-cart-item-meta">
                            <?php if(!empty($sc_size_name[$sc_i]) && $sc_size_name[$sc_i] != '-' && strtolower($sc_size_name[$sc_i]) != 'n/a'): ?>
                            <span><i class="fa fa-th-large"></i> <?php echo $sc_size_name[$sc_i]; ?></span>
                            <?php endif; ?>
                            <?php if(!empty($sc_color_name[$sc_i]) && $sc_color_name[$sc_i] != '-' && strtolower($sc_color_name[$sc_i]) != 'n/a'): ?>
                            <span><i class="fa fa-paint-brush"></i> <?php echo $sc_color_name[$sc_i]; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="side-cart-item-qty">
                            <button type="button" class="qty-btn qty-minus" onclick="updateSideCartQty(this, 'minus')" title="Azalt">−</button>
                            <span class="qty-value"><?php echo $sc_p_qty[$sc_i]; ?></span>
                            <button type="button" class="qty-btn qty-plus" onclick="updateSideCartQty(this, 'plus')" title="Artır">+</button>
                            <span class="side-cart-item-price-inline"><?php echo LANG_VALUE_1; ?><?php echo $sc_row_total; ?></span>
                            <?php if($sc_old_price > 0): ?>
                            <span class="side-cart-item-original-price"><?php echo LANG_VALUE_1; ?><?php echo $sc_old_price * $sc_p_qty[$sc_i]; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="side-cart-item-remove" onclick="updateSideCartQty(this, 'remove')" title="Ürünü Kaldır">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

    <?php if($sc_count > 0): ?>
    <div class="side-cart-footer">
        <?php if(isset($_SESSION['customer'])): ?>
        <?php
        // Check if billing and shipping addresses are the same
        $sc_addr_same = (
            isset($_SESSION['customer']['cust_b_name']) && isset($_SESSION['customer']['cust_s_name']) &&
            $_SESSION['customer']['cust_b_name'] == $_SESSION['customer']['cust_s_name'] &&
            $_SESSION['customer']['cust_b_address'] == $_SESSION['customer']['cust_s_address'] &&
            $_SESSION['customer']['cust_b_city'] == $_SESSION['customer']['cust_s_city']
        );
        ?>
        <div class="side-cart-addresses">
            <div class="sc-address-section" id="sc-billing-section">
                <div class="sc-address-header" onclick="toggleScAddress('billing')">
                    <div class="sc-addr-header-left">
                        <span class="sc-addr-title"><i class="fa fa-map-marker"></i> <?php echo $sc_addr_same ? 'Fatura & Teslimat Adresi' : 'Fatura Adresi'; ?></span>
                        <?php if(!empty($_SESSION['customer']['cust_b_address'])): ?>
                        <span class="sc-addr-preview"><?php echo $_SESSION['customer']['cust_b_name']; ?> — <?php echo mb_strimwidth($_SESSION['customer']['cust_b_address'], 0, 35, '...'); ?>, <?php echo $_SESSION['customer']['cust_b_city']; ?></span>
                        <?php else: ?>
                        <span class="sc-addr-preview sc-addr-preview-empty"><i class="fa fa-exclamation-circle"></i> Adres eklenmemiş</span>
                        <?php endif; ?>
                    </div>
                    <i class="fa fa-chevron-down sc-addr-arrow" id="sc-billing-arrow"></i>
                </div>
                <div class="sc-address-body" id="sc-billing-body">
                    <form class="sc-addr-form" id="sc-billing-form" onsubmit="saveScAddress(event, 'billing')">
                        <div class="sc-addr-form-row">
                            <label>Ad Soyad</label>
                            <input type="text" name="b_name" value="<?php echo isset($_SESSION['customer']['cust_b_name']) ? $_SESSION['customer']['cust_b_name'] : ''; ?>" placeholder="Ad Soyad" required>
                        </div>
                        <div class="sc-addr-form-row">
                            <label>Adres</label>
                            <input type="text" name="b_address" value="<?php echo isset($_SESSION['customer']['cust_b_address']) ? $_SESSION['customer']['cust_b_address'] : ''; ?>" placeholder="Adres" required>
                        </div>
                        <div class="sc-addr-form-grid">
                            <div class="sc-addr-form-row">
                                <label>İl</label>
                                <input type="text" name="b_city" value="<?php echo isset($_SESSION['customer']['cust_b_city']) ? $_SESSION['customer']['cust_b_city'] : ''; ?>" placeholder="İl" required>
                            </div>
                            <div class="sc-addr-form-row">
                                <label>İlçe</label>
                                <input type="text" name="b_state" value="<?php echo isset($_SESSION['customer']['cust_b_state']) ? $_SESSION['customer']['cust_b_state'] : ''; ?>" placeholder="İlçe" required>
                            </div>
                        </div>
                        <div class="sc-addr-form-grid">
                            <div class="sc-addr-form-row">
                                <label>Posta Kodu</label>
                                <input type="text" name="b_zip" value="<?php echo isset($_SESSION['customer']['cust_b_zip']) ? $_SESSION['customer']['cust_b_zip'] : ''; ?>" placeholder="Posta Kodu">
                            </div>
                            <div class="sc-addr-form-row">
                                <label>Telefon</label>
                                <input type="text" name="b_phone" value="<?php echo isset($_SESSION['customer']['cust_b_phone']) ? $_SESSION['customer']['cust_b_phone'] : ''; ?>" placeholder="Telefon" required>
                            </div>
                        </div>
                        <div class="sc-addr-form-row">
                            <label>Ülke</label>
                            <select name="b_country">
                                <?php
                                $stmt_c = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                $stmt_c->execute();
                                $countries = $stmt_c->fetchAll(PDO::FETCH_ASSOC);
                                foreach($countries as $c_row):
                                ?>
                                <option value="<?php echo $c_row['country_name']; ?>" <?php echo (isset($_SESSION['customer']['cust_b_country']) && $_SESSION['customer']['cust_b_country'] == $c_row['country_name']) ? 'selected' : ''; ?>><?php echo $c_row['country_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sc-addr-form-actions">
                            <button type="submit" class="sc-addr-save-btn"><i class="fa fa-check"></i> Kaydet</button>
                            <span class="sc-addr-save-msg" id="sc-billing-msg"></span>
                        </div>
                        <div class="sc-addr-copy-row" style="padding:8px 0 0; margin-top:8px; border-top:1px solid #eee;">
                            <label class="sc-addr-copy-label">
                                <input type="checkbox" id="sc-diff-shipping" onchange="scToggleDiffShipping(this)" <?php echo $sc_addr_same ? '' : 'checked'; ?>>
                                Farklı teslimat adresi kullan
                            </label>
                        </div>
                    </form>
                </div>
            </div>

            

            <div class="sc-address-section" id="sc-shipping-section" style="<?php echo $sc_addr_same ? 'display:none;' : ''; ?>">
                <div class="sc-address-header" onclick="toggleScAddress('shipping')">
                    <div class="sc-addr-header-left">
                        <span class="sc-addr-title"><i class="fa fa-truck"></i> Teslimat Adresi</span>
                        <?php if(!empty($_SESSION['customer']['cust_s_address'])): ?>
                        <span class="sc-addr-preview"><?php echo $_SESSION['customer']['cust_s_name']; ?> — <?php echo mb_strimwidth($_SESSION['customer']['cust_s_address'], 0, 35, '...'); ?>, <?php echo $_SESSION['customer']['cust_s_city']; ?></span>
                        <?php else: ?>
                        <span class="sc-addr-preview sc-addr-preview-empty"><i class="fa fa-exclamation-circle"></i> Adres eklenmemiş</span>
                        <?php endif; ?>
                    </div>
                    <i class="fa fa-chevron-down sc-addr-arrow" id="sc-shipping-arrow"></i>
                </div>
                <div class="sc-address-body" id="sc-shipping-body">
                    <form class="sc-addr-form" id="sc-shipping-form" onsubmit="saveScAddress(event, 'shipping')">
                        <div class="sc-addr-form-row">
                            <label>Ad Soyad</label>
                            <input type="text" name="s_name" value="<?php echo isset($_SESSION['customer']['cust_s_name']) ? $_SESSION['customer']['cust_s_name'] : ''; ?>" placeholder="Ad Soyad" required>
                        </div>
                        <div class="sc-addr-form-row">
                            <label>Adres</label>
                            <input type="text" name="s_address" value="<?php echo isset($_SESSION['customer']['cust_s_address']) ? $_SESSION['customer']['cust_s_address'] : ''; ?>" placeholder="Adres" required>
                        </div>
                        <div class="sc-addr-form-grid">
                            <div class="sc-addr-form-row">
                                <label>İl</label>
                                <input type="text" name="s_city" value="<?php echo isset($_SESSION['customer']['cust_s_city']) ? $_SESSION['customer']['cust_s_city'] : ''; ?>" placeholder="İl" required>
                            </div>
                            <div class="sc-addr-form-row">
                                <label>İlçe</label>
                                <input type="text" name="s_state" value="<?php echo isset($_SESSION['customer']['cust_s_state']) ? $_SESSION['customer']['cust_s_state'] : ''; ?>" placeholder="İlçe" required>
                            </div>
                        </div>
                        <div class="sc-addr-form-grid">
                            <div class="sc-addr-form-row">
                                <label>Posta Kodu</label>
                                <input type="text" name="s_zip" value="<?php echo isset($_SESSION['customer']['cust_s_zip']) ? $_SESSION['customer']['cust_s_zip'] : ''; ?>" placeholder="Posta Kodu">
                            </div>
                            <div class="sc-addr-form-row">
                                <label>Telefon</label>
                                <input type="text" name="s_phone" value="<?php echo isset($_SESSION['customer']['cust_s_phone']) ? $_SESSION['customer']['cust_s_phone'] : ''; ?>" placeholder="Telefon" required>
                            </div>
                        </div>
                        <div class="sc-addr-form-row">
                            <label>Ülke</label>
                            <select name="s_country">
                                <?php foreach($countries as $c_row): ?>
                                <option value="<?php echo $c_row['country_name']; ?>" <?php echo (isset($_SESSION['customer']['cust_s_country']) && $_SESSION['customer']['cust_s_country'] == $c_row['country_name']) ? 'selected' : ''; ?>><?php echo $c_row['country_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sc-addr-form-actions">
                            <button type="submit" class="sc-addr-save-btn"><i class="fa fa-check"></i> Kaydet</button>
                            <span class="sc-addr-save-msg" id="sc-shipping-msg"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="sc-login-prompt">
            <i class="fa fa-info-circle"></i> Ödeme için <a href="login.php">giriş yapın</a>
        </div>
        <?php endif; ?>

        <div class="side-cart-actions">
            <button type="button" class="btn-checkout" onclick="openPaymentDialog()">
                <span class="checkout-label">Ödemeye Geç <i class="fa fa-arrow-right"></i></span>
                <span class="checkout-total"><?php echo LANG_VALUE_1; ?><?php echo $sc_total; ?></span>
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Payment Dialog Modal -->
<div class="sc-payment-overlay" id="scPaymentOverlay" onclick="closePaymentDialog()"></div>
<div class="sc-payment-dialog" id="scPaymentDialog">
    <div class="sc-payment-header">
        <h3><i class="fa fa-credit-card"></i> Ödeme Yöntemi</h3>
        <button type="button" class="sc-payment-close" onclick="closePaymentDialog()">&times;</button>
    </div>
    <div class="sc-payment-body" id="scPaymentBody">
        <!-- Payment Result View (shown via JS when payment_result param exists) -->
        <div id="sc-payment-result" style="display:none;"></div>

        <!-- Payment Form View -->
        <div id="sc-payment-form-view">
        <?php if(!isset($_SESSION['customer'])): ?>
            <div class="sc-login-prompt" style="padding:30px 0;">
                <i class="fa fa-info-circle"></i> Ödeme için <a href="login.php">giriş yapın</a>
            </div>
        <?php else: ?>
            <?php
            // Check address completeness
            $sc_addr_ok = 1;
            if(
                empty($_SESSION['customer']['cust_b_name']) ||
                empty($_SESSION['customer']['cust_b_address']) ||
                empty($_SESSION['customer']['cust_b_city']) ||
                empty($_SESSION['customer']['cust_s_name']) ||
                empty($_SESSION['customer']['cust_s_address']) ||
                empty($_SESSION['customer']['cust_s_city'])
            ) { $sc_addr_ok = 0; }
            ?>
            <?php if($sc_addr_ok == 0): ?>
                <div style="padding:16px;background:#fff3cd;color:#856404;border-radius:6px;font-size:13px;margin-bottom:12px;">
                    <i class="fa fa-exclamation-triangle"></i> Lütfen fatura ve teslimat adreslerinizi yukarıda doldurun.
                </div>
            <?php else: ?>
                <div class="sc-payment-total">
                    <span>Toplam Tutar:</span>
                    <span class="sc-payment-amount"><?php echo LANG_VALUE_1; ?><?php echo $sc_total; ?></span>
                </div>
                <div class="sc-payment-methods">
                    <?php
                    // Check Paratika active
                    $stmt_pk2 = $pdo->prepare("SELECT is_active FROM tbl_payment_gateway_settings WHERE gateway_name='paratika' LIMIT 1");
                    $stmt_pk2->execute();
                    $pk_row2 = $stmt_pk2->fetch(PDO::FETCH_ASSOC);
                    if($pk_row2 && $pk_row2['is_active'] == 1):
                    ?>
                    <label class="sc-payment-option">
                        <input type="radio" name="sc_payment_method" value="kredi_karti" onchange="scTogglePayment(this.value)" checked>
                        <span class="sc-payment-option-label"><i class="fa fa-credit-card"></i> Kredi Kartı (3D Secure)</span>
                    </label>
                    <?php endif; ?>
                    <?php
                    // Check Bank Transfer active
                    $stmt_bt2 = $pdo->prepare("SELECT bank_transfer_on_off FROM tbl_settings WHERE id=1");
                    $stmt_bt2->execute();
                    $bt_row2 = $stmt_bt2->fetch(PDO::FETCH_ASSOC);
                    if(!$bt_row2 || $bt_row2['bank_transfer_on_off'] == 1):
                    ?>
                    <label class="sc-payment-option">
                        <input type="radio" name="sc_payment_method" value="bank" onchange="scTogglePayment(this.value)">
                        <span class="sc-payment-option-label"><i class="fa fa-university"></i> Banka Havalesi</span>
                    </label>
                    <?php endif; ?>
                </div>

                <!-- Kredi Kartı Form -->
                <div class="sc-payment-form" id="sc-pay-kredi_karti" style="display:block;">
                    <form action="payment/paratika/init.php" method="post" id="sc-kredi-form" autocomplete="off">
                        <input type="hidden" name="amount" value="<?php echo $sc_total; ?>">
                        <div class="sc-pay-field">
                            <label>Kart Üzerindeki İsim *</label>
                            <input type="text" name="card_holder" placeholder="Ad Soyad" required autocomplete="off" data-lpignore="true" data-form-type="other">
                        </div>
                        <div class="sc-pay-field">
                            <label>Kart Numarası *</label>
                            <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required autocomplete="off" data-lpignore="true" data-form-type="other"
                                oninput="this.value=this.value.replace(/[^\d]/g,'').replace(/(.{4})/g,'$1 ').trim()">
                        </div>
                        <div class="sc-pay-field-grid">
                            <div class="sc-pay-field">
                                <label>Ay *</label>
                                <select name="expiry_month" required>
                                    <option value="">Ay</option>
                                    <?php for($m=1;$m<=12;$m++): ?>
                                    <option value="<?php echo str_pad($m,2,'0',STR_PAD_LEFT); ?>"><?php echo str_pad($m,2,'0',STR_PAD_LEFT); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="sc-pay-field">
                                <label>Yıl *</label>
                                <select name="expiry_year" required>
                                    <option value="">Yıl</option>
                                    <?php $cy=date('Y'); for($y=$cy;$y<=$cy+10;$y++): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="sc-pay-field">
                                <label>CVV *</label>
                                <input type="text" name="cvv" placeholder="***" maxlength="4" required autocomplete="off" data-lpignore="true" data-form-type="other"
                                    oninput="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>
                        <p style="font-size:11px;color:#888;margin:8px 0;"><i class="fa fa-lock"></i> 3D Secure doğrulaması yapılacaktır. Kart bilgileriniz saklanmaz.</p>
                        <button type="submit" class="sc-pay-submit-btn"><i class="fa fa-lock"></i> Kredi Kartı ile Öde</button>
                    </form>
                </div>

                <!-- Banka Havalesi Form -->
                <div class="sc-payment-form" id="sc-pay-bank" style="display:none;">
                    <form action="payment/bank/init.php" method="post">
                        <input type="hidden" name="amount" value="<?php echo $sc_total; ?>">
                        <div class="sc-pay-field">
                            <label>Banka Bilgileri</label>
                            <div style="font-size:12px;color:#555;padding:8px;background:#f9f9f9;border-radius:4px;line-height:1.6;">
                                <?php
                                $stmt_bank = $pdo->prepare("SELECT bank_detail FROM tbl_settings WHERE id=1");
                                $stmt_bank->execute();
                                $bank_r = $stmt_bank->fetch(PDO::FETCH_ASSOC);
                                echo nl2br($bank_r['bank_detail']);
                                ?>
                            </div>
                        </div>
                        <div class="sc-pay-field">
                            <label>İşlem Bilgisi</label>
                            <textarea name="transaction_info" rows="3" placeholder="Havale/EFT dekont bilgileriniz..."></textarea>
                        </div>
                        <button type="submit" name="form3" class="sc-pay-submit-btn"><i class="fa fa-university"></i> Havale ile Öde</button>
                    </form>
                </div>

                <!-- PayPal removed -->
            <?php endif; ?>
        <?php endif; ?>
        </div><!-- /sc-payment-form-view -->
    </div>
</div>

<script>
// Payment result handling
(function() {
    var params = new URLSearchParams(window.location.search);
    var paymentResult = params.get('payment_result');
    if (!paymentResult) return;

    var resultDiv = document.getElementById('sc-payment-result');
    var formView = document.getElementById('sc-payment-form-view');
    var html = '';

    if (paymentResult === 'error') {
        var errorMsg = params.get('error') || 'Ödeme başarısız oldu';
        var errorCode = params.get('code') || '';
        html += '<div style="text-align:center;padding:30px 16px;">';
        html += '<div style="width:70px;height:70px;margin:0 auto 16px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;">';
        html += '<i class="fa fa-times" style="font-size:34px;color:#ef4444;"></i></div>';
        html += '<h3 style="color:#ef4444;margin-bottom:10px;font-size:18px;">Ödeme Başarısız</h3>';
        html += '<p style="color:#666;font-size:13px;margin-bottom:4px;">' + decodeURIComponent(errorMsg).replace(/</g,'&lt;') + '</p>';
        if (errorCode) html += '<p style="color:#999;font-size:11px;margin-bottom:16px;">Hata Kodu: ' + decodeURIComponent(errorCode).replace(/</g,'&lt;') + '</p>';
        html += '<p style="color:#666;font-size:12px;margin-bottom:20px;">Ödeme işlemi tamamlanamadı. Lütfen tekrar deneyiniz.</p>';
        html += '<button type="button" class="sc-pay-submit-btn" style="margin:0 auto;display:inline-block;width:auto;padding:10px 28px;" onclick="scPaymentRetry()"><i class="fa fa-refresh"></i> Tekrar Dene</button>';
        html += '</div>';
    } else if (paymentResult === 'success') {
        var payAmount = params.get('amount') || '';
        var payId = params.get('payment_id') || '';
        html += '<div style="text-align:center;padding:30px 16px;">';
        html += '<div style="width:70px;height:70px;margin:0 auto 16px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;">';
        html += '<i class="fa fa-check" style="font-size:34px;color:#22c55e;"></i></div>';
        html += '<h3 style="color:#22c55e;margin-bottom:10px;font-size:18px;">Ödeme Başarılı!</h3>';
        html += '<p style="color:#666;font-size:13px;margin-bottom:16px;">Siparişiniz başarıyla oluşturuldu.</p>';
        html += '<div style="background:#f9fafb;border-radius:8px;padding:14px 16px;text-align:left;margin-bottom:20px;border:1px solid #e5e7eb;">';
        if (payId) html += '<div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;"><span style="color:#888;">Sipariş No:</span><span style="font-weight:600;color:#333;">#' + payId + '</span></div>';
        if (payAmount) html += '<div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;"><span style="color:#888;">Ödenen Tutar:</span><span style="font-weight:600;color:#333;"><?php echo LANG_VALUE_1; ?>' + payAmount + '</span></div>';
        html += '<div style="display:flex;justify-content:space-between;font-size:13px;"><span style="color:#888;">Ödeme Yöntemi:</span><span style="font-weight:600;color:#333;">Kredi Kartı (3D Secure)</span></div>';
        html += '</div>';
        html += '<a href="customer-order.php" class="sc-pay-submit-btn" style="display:inline-block;width:auto;padding:10px 28px;text-decoration:none;"><i class="fa fa-list-alt"></i> Siparişlerimi Görüntüle</a>';
        html += '</div>';
    }

    resultDiv.innerHTML = html;
    resultDiv.style.display = 'block';
    formView.style.display = 'none';

    // Auto-open the payment dialog
    document.getElementById('scPaymentOverlay').classList.add('active');
    document.getElementById('scPaymentDialog').classList.add('active');

    // Also open side cart
    var cart = document.getElementById('sideCart');
    var overlay = document.getElementById('sideCartOverlay');
    if (paymentResult === 'error') {
        cart.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Clean URL
    var cleanUrl = window.location.pathname;
    window.history.replaceState({}, '', cleanUrl);
})();

function scPaymentRetry() {
    var resultDiv = document.getElementById('sc-payment-result');
    var formView = document.getElementById('sc-payment-form-view');
    resultDiv.style.display = 'none';
    formView.style.display = 'block';
    // Reset payment method selection
    var radios = document.querySelectorAll('input[name="sc_payment_method"]');
    radios.forEach(function(r) { r.checked = false; });
    var forms = document.querySelectorAll('.sc-payment-form');
    forms.forEach(function(f) { f.style.display = 'none'; });
}
function openPaymentDialog() {
    <?php if(!isset($_SESSION['customer'])): ?>
    window.location.href = 'login.php';
    return;
    <?php endif; ?>
    document.getElementById('scPaymentOverlay').classList.add('active');
    document.getElementById('scPaymentDialog').classList.add('active');
}
function closePaymentDialog() {
    document.getElementById('scPaymentOverlay').classList.remove('active');
    document.getElementById('scPaymentDialog').classList.remove('active');
}
function scTogglePayment(val) {
    var forms = document.querySelectorAll('.sc-payment-form');
    for(var i=0;i<forms.length;i++) forms[i].style.display='none';
    var target = document.getElementById('sc-pay-' + val);
    if(target) target.style.display='block';
}
</script>
<script>
function toggleSideCart() {
    var cart = document.getElementById('sideCart');
    var overlay = document.getElementById('sideCartOverlay');
    cart.classList.toggle('active');
    overlay.classList.toggle('active');
    if(cart.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
        refreshSideCart();
    } else {
        document.body.style.overflow = '';
    }
}

function refreshSideCart() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'cart-update-ajax.php?action=refresh', true);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4 && xhr.status === 200) {
            try {
                var resp = JSON.parse(xhr.responseText);
                if(resp.success) {
                    renderSideCart(resp);
                }
            } catch(e) {}
        }
    };
    xhr.send();
}

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        var cart = document.getElementById('sideCart');
        if(cart.classList.contains('active')) {
            toggleSideCart();
        }
    }
});

function toggleScAddress(type) {
    var body = document.getElementById('sc-' + type + '-body');
    var arrow = document.getElementById('sc-' + type + '-arrow');
    body.classList.toggle('open');
    arrow.classList.toggle('open');
}

function scToggleDiffShipping(cb) {
    var section = document.getElementById('sc-shipping-section');
    var titleEl = document.querySelector('#sc-billing-section .sc-addr-title');
    if(cb.checked) {
        section.style.display = '';
        if(titleEl) titleEl.innerHTML = '<i class="fa fa-map-marker"></i> Fatura Adresi';
    } else {
        section.style.display = 'none';
        if(titleEl) titleEl.innerHTML = '<i class="fa fa-map-marker"></i> Fatura & Teslimat Adresi';
        // Copy billing to shipping silently
        var bf = document.getElementById('sc-billing-form');
        var sf = document.getElementById('sc-shipping-form');
        if(bf && sf) {
            sf.querySelector('[name="s_name"]').value = bf.querySelector('[name="b_name"]').value;
            sf.querySelector('[name="s_address"]').value = bf.querySelector('[name="b_address"]').value;
            sf.querySelector('[name="s_city"]').value = bf.querySelector('[name="b_city"]').value;
            sf.querySelector('[name="s_state"]').value = bf.querySelector('[name="b_state"]').value;
            sf.querySelector('[name="s_zip"]').value = bf.querySelector('[name="b_zip"]').value;
            sf.querySelector('[name="s_phone"]').value = bf.querySelector('[name="b_phone"]').value;
            sf.querySelector('[name="s_country"]').value = bf.querySelector('[name="b_country"]').value;
            // Auto-save shipping = billing
            saveScAddress(new Event('submit', {cancelable:true}), 'shipping');
        }
    }
}

function saveScAddress(e, type) {
    e.preventDefault();
    var form = document.getElementById('sc-' + type + '-form');
    var formData = new FormData(form);
    formData.append('type', type);
    var btn = form.querySelector('.sc-addr-save-btn');
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...';
    btn.disabled = true;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart-update-address.php', true);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4 && xhr.status === 200) {
            var resp = JSON.parse(xhr.responseText);
            var msg = document.getElementById('sc-' + type + '-msg');
            if(resp.success) {
                msg.innerHTML = '<i class="fa fa-check-circle"></i> Kaydedildi';
                msg.style.color = '#15803d';
                // Update preview text in header
                var section = form.closest('.sc-address-section');
                var preview = section.querySelector('.sc-addr-preview');
                var prefix = type === 'billing' ? 'b_' : 's_';
                var name = form.querySelector('[name="' + prefix + 'name"]').value;
                var addr = form.querySelector('[name="' + prefix + 'address"]').value;
                var city = form.querySelector('[name="' + prefix + 'city"]').value;
                if(addr.length > 35) addr = addr.substring(0, 35) + '...';
                preview.textContent = name + ' — ' + addr + ', ' + city;
                preview.classList.remove('sc-addr-preview-empty');
            } else {
                msg.innerHTML = '<i class="fa fa-times-circle"></i> Hata oluştu';
                msg.style.color = '#e74c3c';
            }
            btn.innerHTML = '<i class="fa fa-check"></i> Kaydet';
            btn.disabled = false;
            setTimeout(function(){ msg.textContent = ''; }, 3000);
        }
    };
    xhr.send(formData);
}

function updateSideCartQty(btn, action) {
    var item = btn.closest('.side-cart-item');
    var id = item.getAttribute('data-id');
    var size = item.getAttribute('data-size');
    var color = item.getAttribute('data-color');
    
    // Disable buttons during request
    var buttons = item.querySelectorAll('.qty-btn, .side-cart-item-remove');
    buttons.forEach(function(b) { b.style.pointerEvents = 'none'; b.style.opacity = '0.5'; });

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'cart-update-ajax.php?action=' + action + '&id=' + id + '&size=' + size + '&color=' + color, true);
    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4 && xhr.status === 200) {
            var resp = JSON.parse(xhr.responseText);
            if(resp.success) {
                renderSideCart(resp);
            } else {
                buttons.forEach(function(b) { b.style.pointerEvents = ''; b.style.opacity = ''; });
            }
        }
    };
    xhr.send();
}

function updateHeaderCart(data) {
    var currency = '<?php echo LANG_VALUE_1; ?>';
    // Update header icon badge and total
    var badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(function(b) { b.textContent = data.cart_count; });
    var iconLabels = document.querySelectorAll('.cart-trigger .icon-label');
    iconLabels.forEach(function(l) { l.textContent = currency + (data.cart_total > 0 ? data.cart_total : '0.00'); });
    // Update payment dialog total
    var payAmountEl = document.querySelector('.sc-payment-amount');
    if(payAmountEl) payAmountEl.textContent = currency + data.cart_total;
    var payHiddenInputs = document.querySelectorAll('.sc-payment-form input[name="amount"]');
    payHiddenInputs.forEach(function(inp) { inp.value = data.cart_total; });
}

function renderSideCart(data) {
    var currency = '<?php echo LANG_VALUE_1; ?>';
    var itemsContainer = document.querySelector('.side-cart-items');
    var footer = document.querySelector('.side-cart-footer');
    var headerCount = document.querySelector('.cart-item-count');

    // Update side cart header badge
    if(headerCount) headerCount.textContent = data.cart_count + ' Ürün';

    // Update header icon area
    updateHeaderCart(data);

    if(data.cart_count === 0) {
        itemsContainer.innerHTML = '<div class="side-cart-empty"><i class="fa fa-shopping-basket"></i><h4>Sepetiniz Boş</h4><p>Henüz sepetinize ürün eklemediniz.</p><a href="index.php" class="btn-continue" onclick="toggleSideCart()">Alışverişe Başla</a></div>';
        if(footer) footer.style.display = 'none';
        var shippingBar = document.querySelector('.side-cart-shipping-bar');
        if(shippingBar) shippingBar.style.display = 'none';
        return;
    }

    var html = '';
    for(var i = 0; i < data.items.length; i++) {
        var it = data.items[i];
        html += '<div class="side-cart-item" data-id="' + it.id + '" data-size="' + it.size_id + '" data-color="' + it.color_id + '">';
        html += '<div class="side-cart-item-img"><img src="assets/uploads/' + it.photo + '" alt="' + it.name + '"></div>';
        html += '<div class="side-cart-item-info">';
        html += '<p class="side-cart-item-name" title="' + it.name + '">' + it.name + '</p>';
        // Only show meta if size/color are meaningful
        var metaHtml = '';
        if(it.size_name && it.size_name !== '-' && it.size_name.toLowerCase() !== 'n/a') {
            metaHtml += '<span><i class="fa fa-th-large"></i> ' + it.size_name + '</span>';
        }
        if(it.color_name && it.color_name !== '-' && it.color_name.toLowerCase() !== 'n/a') {
            metaHtml += '<span><i class="fa fa-paint-brush"></i> ' + it.color_name + '</span>';
        }
        if(metaHtml) html += '<div class="side-cart-item-meta">' + metaHtml + '</div>';
        html += '<div class="side-cart-item-qty">';
        html += '<button type="button" class="qty-btn qty-minus" onclick="updateSideCartQty(this, \'minus\')" title="Azalt">−</button>';
        html += '<span class="qty-value">' + it.qty + '</span>';
        html += '<button type="button" class="qty-btn qty-plus" onclick="updateSideCartQty(this, \'plus\')" title="Artır">+</button>';
        html += '<span class="side-cart-item-price-inline">' + currency + it.row_total + '</span>';
        if(it.old_price && it.old_price > it.price) {
            html += '<span class="side-cart-item-original-price">' + currency + (it.old_price * it.qty) + '</span>';
        }
        html += '</div>';
        html += '</div>';
        html += '<button type="button" class="side-cart-item-remove" onclick="updateSideCartQty(this, \'remove\')" title="Ürünü Kaldır"><i class="fa fa-times"></i></button>';
        html += '</div>';
    }
    itemsContainer.innerHTML = html;

    // Update checkout button total
    var checkoutTotal = document.querySelector('.checkout-total');
    if(checkoutTotal) checkoutTotal.textContent = currency + data.cart_total;

    // Show footer
    if(footer) footer.style.display = '';
}
</script>

<script>
// Configure Tawk.to position BEFORE the script loads
var Tawk_API = Tawk_API || {};
Tawk_API.customStyle = {
    visibility: {
        desktop: { position: 'bl' },
        mobile: { position: 'bl' }
    }
};
</script>

<?php echo $before_body; ?>

<script>
// Hide attention grabber
(function() {
    var style = document.createElement('style');
    style.textContent = '.tawk-attention, div[class*="tawk-attention"], iframe[title*="attention"] { display: none !important; visibility: hidden !important; }';
    document.head.appendChild(style);
})();
</script>
</body>
</html>
