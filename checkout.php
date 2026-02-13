<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_checkout = $row['banner_checkout'];
}
?>

<?php
if(!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>

<?php
// Handle address update form submission
if (isset($_POST['update_address'])) {
    // Update billing and shipping address in database
    $statement = $pdo->prepare("UPDATE tbl_customer SET 
                            cust_b_name=?, 
                            cust_b_cname=?, 
                            cust_b_phone=?, 
                            cust_b_country=?, 
                            cust_b_address=?, 
                            cust_b_city=?, 
                            cust_b_state=?, 
                            cust_b_zip=?,
                            cust_s_name=?, 
                            cust_s_cname=?, 
                            cust_s_phone=?, 
                            cust_s_country=?, 
                            cust_s_address=?, 
                            cust_s_city=?, 
                            cust_s_state=?, 
                            cust_s_zip=? 
                            WHERE cust_id=?");
    $statement->execute(array(
                            strip_tags($_POST['cust_b_name']),
                            strip_tags($_POST['cust_b_cname']),
                            strip_tags($_POST['cust_b_phone']),
                            strip_tags($_POST['cust_b_country']),
                            strip_tags($_POST['cust_b_address']),
                            strip_tags($_POST['cust_b_city']),
                            strip_tags($_POST['cust_b_state']),
                            strip_tags($_POST['cust_b_zip']),
                            strip_tags($_POST['cust_s_name']),
                            strip_tags($_POST['cust_s_cname']),
                            strip_tags($_POST['cust_s_phone']),
                            strip_tags($_POST['cust_s_country']),
                            strip_tags($_POST['cust_s_address']),
                            strip_tags($_POST['cust_s_city']),
                            strip_tags($_POST['cust_s_state']),
                            strip_tags($_POST['cust_s_zip']),
                            $_SESSION['customer']['cust_id']
                        ));  
   
    // Update session variables
    $_SESSION['customer']['cust_b_name'] = strip_tags($_POST['cust_b_name']);
    $_SESSION['customer']['cust_b_cname'] = strip_tags($_POST['cust_b_cname']);
    $_SESSION['customer']['cust_b_phone'] = strip_tags($_POST['cust_b_phone']);
    $_SESSION['customer']['cust_b_country'] = strip_tags($_POST['cust_b_country']);
    $_SESSION['customer']['cust_b_address'] = strip_tags($_POST['cust_b_address']);
    $_SESSION['customer']['cust_b_city'] = strip_tags($_POST['cust_b_city']);
    $_SESSION['customer']['cust_b_state'] = strip_tags($_POST['cust_b_state']);
    $_SESSION['customer']['cust_b_zip'] = strip_tags($_POST['cust_b_zip']);
    $_SESSION['customer']['cust_s_name'] = strip_tags($_POST['cust_s_name']);
    $_SESSION['customer']['cust_s_cname'] = strip_tags($_POST['cust_s_cname']);
    $_SESSION['customer']['cust_s_phone'] = strip_tags($_POST['cust_s_phone']);
    $_SESSION['customer']['cust_s_country'] = strip_tags($_POST['cust_s_country']);
    $_SESSION['customer']['cust_s_address'] = strip_tags($_POST['cust_s_address']);
    $_SESSION['customer']['cust_s_city'] = strip_tags($_POST['cust_s_city']);
    $_SESSION['customer']['cust_s_state'] = strip_tags($_POST['cust_s_state']);
    $_SESSION['customer']['cust_s_zip'] = strip_tags($_POST['cust_s_zip']);

    $address_success = "Address information updated successfully!";
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo LANG_VALUE_22; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                <?php if(!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else: ?>

                <style>
                    .order-section { margin-bottom: 25px; }
                    .order-section .section-title { 
                        font-size: 16px; 
                        font-weight: 600; 
                        color: #333; 
                        margin-bottom: 12px; 
                        padding-bottom: 8px; 
                        border-bottom: 2px solid #e67e22; 
                        display: inline-block;
                    }
                    .order-table { font-size: 13px; }
                    .order-table th { 
                        background: #f8f9fa; 
                        font-size: 12px; 
                        font-weight: 600; 
                        padding: 8px 10px !important; 
                        text-transform: uppercase;
                        color: #555;
                    }
                    .order-table td { 
                        padding: 8px 10px !important; 
                        vertical-align: middle !important; 
                    }
                    .order-table img { 
                        width: 50px; 
                        height: 60px; 
                        object-fit: cover; 
                        border-radius: 4px; 
                    }
                    .order-table .product-name { 
                        font-weight: 500; 
                        color: #333; 
                        font-size: 12px;
                    }
                    .order-totals td, .order-totals th { 
                        font-size: 12px; 
                        padding: 6px 10px !important; 
                    }
                    .address-card { 
                        background: #fff; 
                        border: 1px solid #e0e0e0; 
                        border-radius: 8px; 
                        padding: 15px; 
                        margin-bottom: 15px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    }
                    .address-card .address-title { 
                        font-size: 14px; 
                        font-weight: 600; 
                        color: #333; 
                        margin-bottom: 10px;
                        padding-bottom: 8px;
                        border-bottom: 2px solid #e67e22;
                        display: inline-block;
                    }
                    .address-grid { 
                        display: grid; 
                        grid-template-columns: repeat(2, 1fr); 
                        gap: 8px; 
                    }
                    .address-item { 
                        font-size: 12px; 
                        padding: 6px 0; 
                    }
                    .address-item label { 
                        font-weight: 600; 
                        color: #666; 
                        display: block;
                        font-size: 10px;
                        text-transform: uppercase;
                        margin-bottom: 2px;
                    }
                    .address-item span { 
                        color: #333; 
                    }
                    .address-item.full-width { 
                        grid-column: span 2; 
                    }
                    @media (max-width: 768px) {
                        .address-grid { grid-template-columns: 1fr; }
                        .address-item.full-width { grid-column: span 1; }
                    }
                </style>

                <div class="order-section">
                    <h3 class="section-title"><?php echo LANG_VALUE_26; ?></h3>
                    <div class="cart">
                        <table class="table table-responsive table-hover table-bordered order-table">
                            <tr>
                                <th><?php echo '#' ?></th>
                                <th><?php echo LANG_VALUE_8; ?></th>
                                <th><?php echo LANG_VALUE_47; ?></th>
                                <th><?php echo LANG_VALUE_157; ?></th>
                                <th><?php echo LANG_VALUE_158; ?></th>
                                <th><?php echo LANG_VALUE_159; ?></th>
                                <th><?php echo LANG_VALUE_55; ?></th>
                                <th class="text-right"><?php echo LANG_VALUE_82; ?></th>
                            </tr>
                             <?php
                            $table_total_price = 0;

                            $i=0;
                            foreach($_SESSION['cart_p_id'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_id[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_size_id'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_size_id[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_size_name'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_size_name[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_color_id'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_color_id[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_color_name'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_color_name[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_qty'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_qty[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_current_price[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_name'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_name[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_featured_photo[$i] = $value;
                            }
                            ?>
                            <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <img src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="">
                                </td>
                                <td class="product-name"><?php echo $arr_cart_p_name[$i]; ?></td>
                                <td><?php echo $arr_cart_size_name[$i]; ?></td>
                                <td><?php echo $arr_cart_color_name[$i]; ?></td>
                                <td><?php echo LANG_VALUE_1; ?><?php echo $arr_cart_p_current_price[$i]; ?></td>
                                <td><?php echo $arr_cart_p_qty[$i]; ?></td>
                                <td class="text-right">
                                    <?php
                                    $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                    $table_total_price = $table_total_price + $row_total_price;
                                    ?>
                                    <?php echo LANG_VALUE_1; ?><?php echo $row_total_price; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>           
                            <tr class="order-totals">
                                <th colspan="7" class="total-text"><?php echo LANG_VALUE_81; ?></th>
                                <th class="total-amount"><?php echo LANG_VALUE_1; ?><?php echo $table_total_price; ?></th>
                            </tr>
                            <?php
                            $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost WHERE country_id=?");
                            $statement->execute(array($_SESSION['customer']['cust_country']));
                            $total = $statement->rowCount();
                            if($total) {
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $shipping_cost = $row['amount'];
                                }
                            } else {
                                $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost_all WHERE sca_id=1");
                                $statement->execute();
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    $shipping_cost = $row['amount'];
                                }
                            }                        
                            ?>
                            <tr class="order-totals">
                                <td colspan="7" class="total-text"><?php echo LANG_VALUE_84; ?></td>
                                <td class="total-amount"><?php echo LANG_VALUE_1; ?><?php echo $shipping_cost; ?></td>
                            </tr>
                            <tr class="order-totals">
                                <th colspan="7" class="total-text"><?php echo LANG_VALUE_82; ?></th>
                                <th class="total-amount">
                                    <?php
                                    $final_total = $table_total_price+$shipping_cost;
                                    ?>
                                    <?php echo LANG_VALUE_1; ?><?php echo $final_total; ?>
                                </th>
                            </tr>
                        </table> 
                    </div>
                </div>

                

                <style>
                    .copy-btn {
                        background: linear-gradient(135deg, #e67e22, #d35400);
                        color: #fff;
                        border: none;
                        padding: 6px 12px;
                        border-radius: 5px;
                        font-size: 11px;
                        font-weight: 600;
                        cursor: pointer;
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        transition: all 0.2s ease;
                    }
                    .copy-btn:hover { transform: translateY(-1px); }
                    .copy-btn i { font-size: 12px; }
                    .address-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 12px;
                        flex-wrap: wrap;
                        gap: 8px;
                    }
                    .compact-form .form-group { margin-bottom: 10px; }
                    .compact-form label {
                        font-size: 10px;
                        font-weight: 600;
                        text-transform: uppercase;
                        color: #666;
                        margin-bottom: 3px;
                    }
                    .compact-form .form-control {
                        font-size: 12px;
                        padding: 6px 10px;
                        border-radius: 5px;
                        border: 1px solid #ddd;
                    }
                    .compact-form textarea.form-control { height: 50px !important; }
                    .copy-success {
                        display: none;
                        color: #27ae60;
                        font-size: 11px;
                        margin-left: 8px;
                    }
                    .save-address-btn {
                        background: #27ae60;
                        color: #fff;
                        border: none;
                        padding: 10px 25px;
                        border-radius: 6px;
                        font-size: 13px;
                        font-weight: 600;
                        cursor: pointer;
                        margin-top: 15px;
                    }
                    .save-address-btn:hover { background: #219a52; }
                </style>

                <?php if(isset($address_success)): ?>
                    <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">
                        <?php echo $address_success; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" class="compact-form">
                    <?php $csrf->echoInputField(); ?>
                    <div class="billing-address">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="address-card">
                                    <div class="address-header">
                                        <h4 class="address-title" style="margin:0;"><?php echo LANG_VALUE_161; ?></h4>
                                        <div>
                                            <button type="button" class="copy-btn" onclick="copyToBilling()">
                                                <i class="fa fa-copy"></i> Copy from Profile
                                            </button>
                                            <span class="copy-success" id="billing-success"><i class="fa fa-check"></i> Copied!</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_102; ?> *</label>
                                            <input type="text" class="form-control" name="cust_b_name" id="cust_b_name" value="<?php echo $_SESSION['customer']['cust_b_name']; ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_103; ?></label>
                                            <input type="text" class="form-control" name="cust_b_cname" id="cust_b_cname" value="<?php echo $_SESSION['customer']['cust_b_cname']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_104; ?> *</label>
                                            <input type="text" class="form-control" name="cust_b_phone" id="cust_b_phone" value="<?php echo $_SESSION['customer']['cust_b_phone']; ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_106; ?> *</label>
                                            <select name="cust_b_country" id="cust_b_country" class="form-control" required>
                                                <?php
                                                $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    ?>
                                                    <option value="<?php echo $row['country_id']; ?>" <?php if($row['country_id'] == $_SESSION['customer']['cust_b_country']) {echo 'selected';} ?>><?php echo $row['country_name']; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label><?php echo LANG_VALUE_105; ?> *</label>
                                            <textarea name="cust_b_address" id="cust_b_address" class="form-control" required><?php echo $_SESSION['customer']['cust_b_address']; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_107; ?> *</label>
                                            <input type="text" class="form-control" name="cust_b_city" id="cust_b_city" value="<?php echo $_SESSION['customer']['cust_b_city']; ?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_108; ?> *</label>
                                            <input type="text" class="form-control" name="cust_b_state" id="cust_b_state" value="<?php echo $_SESSION['customer']['cust_b_state']; ?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_109; ?> *</label>
                                            <input type="text" class="form-control" name="cust_b_zip" id="cust_b_zip" value="<?php echo $_SESSION['customer']['cust_b_zip']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="address-card">
                                    <div class="address-header">
                                        <h4 class="address-title" style="margin:0;"><?php echo LANG_VALUE_162; ?></h4>
                                        <div>
                                            <button type="button" class="copy-btn" onclick="copyToShipping()">
                                                <i class="fa fa-copy"></i> Copy from Profile
                                            </button>
                                            <button type="button" class="copy-btn" onclick="copyBillingToShipping()" style="background: #3498db;">
                                                <i class="fa fa-arrow-left"></i> Same as Billing
                                            </button>
                                            <span class="copy-success" id="shipping-success"><i class="fa fa-check"></i> Copied!</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_102; ?> *</label>
                                            <input type="text" class="form-control" name="cust_s_name" id="cust_s_name" value="<?php echo $_SESSION['customer']['cust_s_name']; ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_103; ?></label>
                                            <input type="text" class="form-control" name="cust_s_cname" id="cust_s_cname" value="<?php echo $_SESSION['customer']['cust_s_cname']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_104; ?> *</label>
                                            <input type="text" class="form-control" name="cust_s_phone" id="cust_s_phone" value="<?php echo $_SESSION['customer']['cust_s_phone']; ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label><?php echo LANG_VALUE_106; ?> *</label>
                                            <select name="cust_s_country" id="cust_s_country" class="form-control" required>
                                                <?php
                                                $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    ?>
                                                    <option value="<?php echo $row['country_id']; ?>" <?php if($row['country_id'] == $_SESSION['customer']['cust_s_country']) {echo 'selected';} ?>><?php echo $row['country_name']; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label><?php echo LANG_VALUE_105; ?> *</label>
                                            <textarea name="cust_s_address" id="cust_s_address" class="form-control" required><?php echo $_SESSION['customer']['cust_s_address']; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_107; ?> *</label>
                                            <input type="text" class="form-control" name="cust_s_city" id="cust_s_city" value="<?php echo $_SESSION['customer']['cust_s_city']; ?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_108; ?> *</label>
                                            <input type="text" class="form-control" name="cust_s_state" id="cust_s_state" value="<?php echo $_SESSION['customer']['cust_s_state']; ?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label><?php echo LANG_VALUE_109; ?> *</label>
                                            <input type="text" class="form-control" name="cust_s_zip" id="cust_s_zip" value="<?php echo $_SESSION['customer']['cust_s_zip']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="update_address" class="save-address-btn">
                            <i class="fa fa-save"></i> Save Address & Continue
                        </button>
                    </div>
                </form>

                <script>
                // Profile data from session for copy functionality
                var profileData = {
                    name: "<?php echo addslashes($_SESSION['customer']['cust_name']); ?>",
                    cname: "<?php echo addslashes($_SESSION['customer']['cust_cname']); ?>",
                    phone: "<?php echo addslashes($_SESSION['customer']['cust_phone']); ?>",
                    country: "<?php echo $_SESSION['customer']['cust_country']; ?>",
                    address: "<?php echo addslashes($_SESSION['customer']['cust_address']); ?>",
                    city: "<?php echo addslashes($_SESSION['customer']['cust_city']); ?>",
                    state: "<?php echo addslashes($_SESSION['customer']['cust_state']); ?>",
                    zip: "<?php echo addslashes($_SESSION['customer']['cust_zip']); ?>"
                };

                function copyToBilling() {
                    document.getElementById('cust_b_name').value = profileData.name;
                    document.getElementById('cust_b_cname').value = profileData.cname;
                    document.getElementById('cust_b_phone').value = profileData.phone;
                    document.getElementById('cust_b_country').value = profileData.country;
                    document.getElementById('cust_b_address').value = profileData.address;
                    document.getElementById('cust_b_city').value = profileData.city;
                    document.getElementById('cust_b_state').value = profileData.state;
                    document.getElementById('cust_b_zip').value = profileData.zip;
                    showSuccess('billing-success');
                }

                function copyToShipping() {
                    document.getElementById('cust_s_name').value = profileData.name;
                    document.getElementById('cust_s_cname').value = profileData.cname;
                    document.getElementById('cust_s_phone').value = profileData.phone;
                    document.getElementById('cust_s_country').value = profileData.country;
                    document.getElementById('cust_s_address').value = profileData.address;
                    document.getElementById('cust_s_city').value = profileData.city;
                    document.getElementById('cust_s_state').value = profileData.state;
                    document.getElementById('cust_s_zip').value = profileData.zip;
                    showSuccess('shipping-success');
                }

                function copyBillingToShipping() {
                    document.getElementById('cust_s_name').value = document.getElementById('cust_b_name').value;
                    document.getElementById('cust_s_cname').value = document.getElementById('cust_b_cname').value;
                    document.getElementById('cust_s_phone').value = document.getElementById('cust_b_phone').value;
                    document.getElementById('cust_s_country').value = document.getElementById('cust_b_country').value;
                    document.getElementById('cust_s_address').value = document.getElementById('cust_b_address').value;
                    document.getElementById('cust_s_city').value = document.getElementById('cust_b_city').value;
                    document.getElementById('cust_s_state').value = document.getElementById('cust_b_state').value;
                    document.getElementById('cust_s_zip').value = document.getElementById('cust_b_zip').value;
                    showSuccess('shipping-success');
                }

                function showSuccess(id) {
                    var el = document.getElementById(id);
                    el.style.display = 'inline';
                    setTimeout(function() { el.style.display = 'none'; }, 2000);
                }
                </script>

                

                <div class="cart-buttons">
                    <ul>
                        <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>
                    </ul>
                </div>

				<div class="clear"></div>
                <h3 class="special"><?php echo LANG_VALUE_33; ?></h3>
                <div class="row">
                    
                    	<?php
		                $checkout_access = 1;
		                if(
		                    ($_SESSION['customer']['cust_b_name']=='') ||
		                    ($_SESSION['customer']['cust_b_cname']=='') ||
		                    ($_SESSION['customer']['cust_b_phone']=='') ||
		                    ($_SESSION['customer']['cust_b_country']=='') ||
		                    ($_SESSION['customer']['cust_b_address']=='') ||
		                    ($_SESSION['customer']['cust_b_city']=='') ||
		                    ($_SESSION['customer']['cust_b_state']=='') ||
		                    ($_SESSION['customer']['cust_b_zip']=='') ||
		                    ($_SESSION['customer']['cust_s_name']=='') ||
		                    ($_SESSION['customer']['cust_s_cname']=='') ||
		                    ($_SESSION['customer']['cust_s_phone']=='') ||
		                    ($_SESSION['customer']['cust_s_country']=='') ||
		                    ($_SESSION['customer']['cust_s_address']=='') ||
		                    ($_SESSION['customer']['cust_s_city']=='') ||
		                    ($_SESSION['customer']['cust_s_state']=='') ||
		                    ($_SESSION['customer']['cust_s_zip']=='')
		                ) {
		                    $checkout_access = 0;
		                }
		                ?>
		                <?php if($checkout_access == 0): ?>
		                	<div class="col-md-12">
				                <div style="color:red;font-size:22px;margin-bottom:50px;">
			                        You must have to fill up all the billing and shipping information from your dashboard panel in order to checkout the order. Please fill up the information going to <a href="customer-billing-shipping-update.php" style="color:red;text-decoration:underline;">this link</a>.
			                    </div>
	                    	</div>
	                	<?php else: ?>
		                	<div class="col-md-4">
		                		
	                            <div class="row">

	                                <div class="col-md-12 form-group">
	                                    <label for=""><?php echo LANG_VALUE_34; ?> *</label>
                                    <select name="payment_method" class="form-control select2" id="advFieldsStatus">
	                                        <option value=""><?php echo LANG_VALUE_35; ?></option>
                                            <?php
                                            // Check if Paratika (Kredi Kartı) is active
                                            $stmt_pk = $pdo->prepare("SELECT is_active FROM tbl_payment_gateway_settings WHERE gateway_name='paratika' LIMIT 1");
                                            $stmt_pk->execute();
                                            $pk_row = $stmt_pk->fetch(PDO::FETCH_ASSOC);
                                            if($pk_row && $pk_row['is_active'] == 1):
                                            ?>
                                            <option value="Kredi Kartı">Kredi Kartı (3D Secure)</option>
                                            <?php endif; ?>
	                                        <option value="PayPal"><?php echo LANG_VALUE_36; ?></option>
	                                        <option value="Banka Havalesi"><?php echo LANG_VALUE_38; ?></option>
	                                    </select>
	                                </div>

                                    <form class="paypal" action="<?php echo BASE_URL; ?>payment/paypal/payment_process.php" method="post" id="paypal_form" target="_blank">
                                        <input type="hidden" name="cmd" value="_xclick" />
                                        <input type="hidden" name="no_note" value="1" />
                                        <input type="hidden" name="lc" value="UK" />
                                        <input type="hidden" name="currency_code" value="USD" />
                                        <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />

                                        <input type="hidden" name="final_total" value="<?php echo $final_total; ?>">
                                        <div class="col-md-12 form-group">
                                            <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form1">
                                        </div>
                                    </form>



                                    <!-- Kredi Kartı Form -->
                                    <form action="payment/paratika/init.php" method="post" id="kredi_karti_form" style="display:none;">
                                        <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                                        <div class="col-md-12 form-group">
                                            <label>Kart Üzerindeki İsim *</label>
                                            <input type="text" name="card_holder" class="form-control" placeholder="Ad Soyad" required>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label>Kart Numarası *</label>
                                            <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required 
                                                oninput="this.value=this.value.replace(/[^\d]/g,'').replace(/(.{4})/g,'$1 ').trim()">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Ay *</label>
                                            <select name="expiry_month" class="form-control" required>
                                                <option value="">Ay</option>
                                                <?php for($m=1;$m<=12;$m++): ?>
                                                <option value="<?php echo str_pad($m,2,'0',STR_PAD_LEFT); ?>"><?php echo str_pad($m,2,'0',STR_PAD_LEFT); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Yıl *</label>
                                            <select name="expiry_year" class="form-control" required>
                                                <option value="">Yıl</option>
                                                <?php $cy=date('Y'); for($y=$cy;$y<=$cy+10;$y++): ?>
                                                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>CVV *</label>
                                            <input type="text" name="cvv" class="form-control" placeholder="***" maxlength="4" required 
                                                oninput="this.value=this.value.replace(/[^\d]/g,'')">
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <p style="font-size:11px;color:#888;"><i class="fa fa-lock"></i> Güvenli ödeme için 3D Secure doğrulaması yapılacaktır. Kart bilgileriniz sunucumuzda saklanmaz.</p>
                                            <input type="submit" class="btn btn-success btn-block" value="Kredi Kartı ile Öde" name="form_paratika" style="font-size:16px;padding:12px;">
                                        </div>
                                    </form>

                                    <form action="payment/bank/init.php" method="post" id="bank_form" style="display:none;">
                                        <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                                        <div class="col-md-12 form-group">
                                            <label for=""><?php echo LANG_VALUE_43; ?></span></label><br>
                                            <?php
                                            $statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
                                            $statement->execute();
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                echo nl2br($row['bank_detail']);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label for=""><?php echo LANG_VALUE_44; ?> <br><span style="font-size:12px;font-weight:normal;">(<?php echo LANG_VALUE_45; ?>)</span></label>
                                            <textarea name="transaction_info" class="form-control" cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form3">
                                        </div>
                                    </form>
	                                
	                            </div>
		                            
		                        
		                    </div>
		                <?php endif; ?>
                        
                </div>
                

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>