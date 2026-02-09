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
                <div class="side-cart-item" style="animation-delay: <?php echo ($sc_i - 1) * 0.05; ?>s">
                    <div class="side-cart-item-img">
                        <img src="assets/uploads/<?php echo $sc_p_photo[$sc_i]; ?>" alt="<?php echo $sc_p_name[$sc_i]; ?>">
                    </div>
                    <div class="side-cart-item-info">
                        <p class="side-cart-item-name" title="<?php echo $sc_p_name[$sc_i]; ?>"><?php echo $sc_p_name[$sc_i]; ?></p>
                        <div class="side-cart-item-meta">
                            <span><i class="fa fa-th-large"></i> <?php echo $sc_size_name[$sc_i]; ?></span>
                            <span><i class="fa fa-paint-brush"></i> <?php echo $sc_color_name[$sc_i]; ?></span>
                        </div>
                        <div class="side-cart-item-price">
                            <span class="side-cart-item-unit"><?php echo LANG_VALUE_1; ?><?php echo $sc_p_price[$sc_i]; ?> × <?php echo $sc_p_qty[$sc_i]; ?></span>
                            <span class="side-cart-item-total"><?php echo LANG_VALUE_1; ?><?php echo $sc_row_total; ?></span>
                        </div>
                    </div>
                    <a href="cart-item-delete.php?id=<?php echo $sc_p_id[$sc_i]; ?>&size=<?php echo $sc_size_id[$sc_i]; ?>&color=<?php echo $sc_color_id[$sc_i]; ?>" 
                       class="side-cart-item-remove" 
                       onclick="return confirm('Bu ürünü sepetinizden silmek istiyor musunuz?');"
                       title="Ürünü Kaldır">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

    <?php if($sc_count > 0): ?>
    <div class="side-cart-footer">
        <div class="side-cart-subtotal">
            <span>Toplam</span>
            <span><?php echo LANG_VALUE_1; ?><?php echo $sc_total; ?></span>
        </div>
        <div class="side-cart-actions">
            <a href="checkout.php" class="btn-checkout">Ödemeye Geç <i class="fa fa-arrow-right"></i></a>
            <a href="cart.php" class="btn-view-cart">Sepeti Görüntüle</a>
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
    
    // Prevent body scroll when cart is open
    if(cart.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        var cart = document.getElementById('sideCart');
        if(cart.classList.contains('active')) {
            toggleSideCart();
        }
    }
});
</script>

<?php echo $before_body; ?>
</body>
</html>
