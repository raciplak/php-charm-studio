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

		$('#paypal_form').hide();
		$('#stripe_form').hide();
		$('#bank_form').hide();

        $('#advFieldsStatus').on('change',function() {
            advFieldsStatus = $('#advFieldsStatus').val();
            if ( advFieldsStatus == '' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'PayPal' ) {
               	$('#paypal_form').show();
				$('#stripe_form').hide();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'Stripe' ) {
               	$('#paypal_form').hide();
				$('#stripe_form').show();
				$('#bank_form').hide();
            } else if ( advFieldsStatus == 'Banka Havalesi' ) {
            	$('#paypal_form').hide();
				$('#stripe_form').hide();
				$('#bank_form').show();
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
                ?>
                <div class="side-cart-item" data-id="<?php echo $sc_p_id[$sc_i]; ?>" data-size="<?php echo $sc_size_id[$sc_i]; ?>" data-color="<?php echo $sc_color_id[$sc_i]; ?>" style="animation-delay: <?php echo ($sc_i - 1) * 0.05; ?>s">
                    <div class="side-cart-item-img">
                        <img src="assets/uploads/<?php echo $sc_p_photo[$sc_i]; ?>" alt="<?php echo $sc_p_name[$sc_i]; ?>">
                    </div>
                    <div class="side-cart-item-info">
                        <p class="side-cart-item-name" title="<?php echo $sc_p_name[$sc_i]; ?>"><?php echo $sc_p_name[$sc_i]; ?></p>
                        <div class="side-cart-item-meta">
                            <span><i class="fa fa-th-large"></i> <?php echo $sc_size_name[$sc_i]; ?></span>
                            <span><i class="fa fa-paint-brush"></i> <?php echo $sc_color_name[$sc_i]; ?></span>
                        </div>
                        <div class="side-cart-item-qty">
                            <button type="button" class="qty-btn qty-minus" onclick="updateSideCartQty(this, 'minus')" title="Azalt">−</button>
                            <span class="qty-value"><?php echo $sc_p_qty[$sc_i]; ?></span>
                            <button type="button" class="qty-btn qty-plus" onclick="updateSideCartQty(this, 'plus')" title="Artır">+</button>
                        </div>
                        <div class="side-cart-item-price">
                            <span class="side-cart-item-unit"><?php echo LANG_VALUE_1; ?><?php echo $sc_p_price[$sc_i]; ?> × <?php echo $sc_p_qty[$sc_i]; ?></span>
                            <span class="side-cart-item-total"><?php echo LANG_VALUE_1; ?><?php echo $sc_row_total; ?></span>
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
        <div class="side-cart-addresses">
            <div class="sc-address-section">
                <div class="sc-address-header" onclick="toggleScAddress('billing')">
                    <span><i class="fa fa-file-text-o"></i> Fatura Adresi</span>
                    <i class="fa fa-chevron-down sc-addr-arrow" id="sc-billing-arrow"></i>
                </div>
                <div class="sc-address-body" id="sc-billing-body">
                    <?php if(!empty($_SESSION['customer']['cust_b_address'])): ?>
                        <p class="sc-addr-name"><?php echo $_SESSION['customer']['cust_b_name']; ?></p>
                        <p class="sc-addr-detail"><?php echo $_SESSION['customer']['cust_b_address']; ?></p>
                        <p class="sc-addr-detail"><?php echo $_SESSION['customer']['cust_b_city']; ?> / <?php echo $_SESSION['customer']['cust_b_state']; ?> <?php echo $_SESSION['customer']['cust_b_zip']; ?></p>
                        <p class="sc-addr-detail"><i class="fa fa-phone"></i> <?php echo $_SESSION['customer']['cust_b_phone']; ?></p>
                    <?php else: ?>
                        <p class="sc-addr-empty"><i class="fa fa-exclamation-circle"></i> Fatura adresi eklenmemiş</p>
                        <a href="customer-billing-shipping-update.php" class="sc-addr-add-link">Adres Ekle</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sc-address-section">
                <div class="sc-address-header" onclick="toggleScAddress('shipping')">
                    <span><i class="fa fa-truck"></i> Teslimat Adresi</span>
                    <i class="fa fa-chevron-down sc-addr-arrow" id="sc-shipping-arrow"></i>
                </div>
                <div class="sc-address-body" id="sc-shipping-body">
                    <?php if(!empty($_SESSION['customer']['cust_s_address'])): ?>
                        <p class="sc-addr-name"><?php echo $_SESSION['customer']['cust_s_name']; ?></p>
                        <p class="sc-addr-detail"><?php echo $_SESSION['customer']['cust_s_address']; ?></p>
                        <p class="sc-addr-detail"><?php echo $_SESSION['customer']['cust_s_city']; ?> / <?php echo $_SESSION['customer']['cust_s_state']; ?> <?php echo $_SESSION['customer']['cust_s_zip']; ?></p>
                        <p class="sc-addr-detail"><i class="fa fa-phone"></i> <?php echo $_SESSION['customer']['cust_s_phone']; ?></p>
                    <?php else: ?>
                        <p class="sc-addr-empty"><i class="fa fa-exclamation-circle"></i> Teslimat adresi eklenmemiş</p>
                        <a href="customer-billing-shipping-update.php" class="sc-addr-add-link">Adres Ekle</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="sc-login-prompt">
            <i class="fa fa-info-circle"></i> Ödeme için <a href="login.php">giriş yapın</a>
        </div>
        <?php endif; ?>

        <div class="side-cart-subtotal">
            <span>Toplam</span>
            <span><?php echo LANG_VALUE_1; ?><?php echo $sc_total; ?></span>
        </div>
        <div class="side-cart-actions">
            <a href="checkout.php" class="btn-checkout">Ödemeye Geç <i class="fa fa-arrow-right"></i></a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleSideCart() {
    var cart = document.getElementById('sideCart');
    var overlay = document.getElementById('sideCartOverlay');
    cart.classList.toggle('active');
    overlay.classList.toggle('active');
    if(cart.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
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

function renderSideCart(data) {
    var currency = '<?php echo LANG_VALUE_1; ?>';
    var itemsContainer = document.querySelector('.side-cart-items');
    var footer = document.querySelector('.side-cart-footer');
    var headerCount = document.querySelector('.cart-item-count');
    var headerBadge = document.querySelector('.cart-count-badge');

    // Update header badge
    if(headerCount) headerCount.textContent = data.cart_count + ' Ürün';
    if(headerBadge) headerBadge.textContent = data.cart_count;

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
        html += '<div class="side-cart-item-meta"><span><i class="fa fa-th-large"></i> ' + it.size_name + '</span><span><i class="fa fa-paint-brush"></i> ' + it.color_name + '</span></div>';
        html += '<div class="side-cart-item-qty">';
        html += '<button type="button" class="qty-btn qty-minus" onclick="updateSideCartQty(this, \'minus\')" title="Azalt">−</button>';
        html += '<span class="qty-value">' + it.qty + '</span>';
        html += '<button type="button" class="qty-btn qty-plus" onclick="updateSideCartQty(this, \'plus\')" title="Artır">+</button>';
        html += '</div>';
        html += '<div class="side-cart-item-price"><span class="side-cart-item-unit">' + currency + it.price + ' × ' + it.qty + '</span><span class="side-cart-item-total">' + currency + it.row_total + '</span></div>';
        html += '</div>';
        html += '<button type="button" class="side-cart-item-remove" onclick="updateSideCartQty(this, \'remove\')" title="Ürünü Kaldır"><i class="fa fa-times"></i></button>';
        html += '</div>';
    }
    itemsContainer.innerHTML = html;

    // Update footer total
    if(footer) {
        var subtotalEl = footer.querySelector('.side-cart-subtotal span:last-child');
        if(subtotalEl) subtotalEl.textContent = currency + data.cart_total;
    }
}
</script>

<?php echo $before_body; ?>
</body>
</html>
