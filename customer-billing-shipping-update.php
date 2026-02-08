<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<?php
if (isset($_POST['form1'])) {


    // update data into the database
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
   
    $success_message = LANG_VALUE_122;

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

}
?>

<style>
    .copy-btn {
        background: linear-gradient(135deg, #e67e22, #d35400);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .copy-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .copy-btn i { font-size: 14px; }
    .address-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .address-section-title h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        padding-bottom: 8px;
        border-bottom: 2px solid #e67e22;
    }
    .compact-form .form-group {
        margin-bottom: 12px;
    }
    .compact-form label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 4px;
    }
    .compact-form .form-control {
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ddd;
    }
    .compact-form textarea.form-control {
        height: 60px !important;
    }
    .address-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .copy-success {
        display: none;
        color: #27ae60;
        font-size: 12px;
        margin-left: 10px;
    }
</style>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <?php
                    if($error_message != '') {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                    }
                    if($success_message != '') {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                    }
                    ?>
                    <form action="" method="post" class="compact-form">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="address-card">
                                    <div class="address-section-title">
                                        <h3><?php echo LANG_VALUE_86; ?></h3>
                                        <button type="button" class="copy-btn" onclick="copyToBilling()">
                                            <i class="fa fa-copy"></i> Copy from Profile
                                        </button>
                                        <span class="copy-success" id="billing-success"><i class="fa fa-check"></i> Copied!</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_102; ?></label>
                                            <input type="text" class="form-control" name="cust_b_name" id="cust_b_name" value="<?php echo $_SESSION['customer']['cust_b_name']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_103; ?></label>
                                            <input type="text" class="form-control" name="cust_b_cname" id="cust_b_cname" value="<?php echo $_SESSION['customer']['cust_b_cname']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_104; ?></label>
                                            <input type="text" class="form-control" name="cust_b_phone" id="cust_b_phone" value="<?php echo $_SESSION['customer']['cust_b_phone']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_106; ?></label>
                                            <select name="cust_b_country" id="cust_b_country" class="form-control">
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
                                            <label for=""><?php echo LANG_VALUE_105; ?></label>
                                            <textarea name="cust_b_address" id="cust_b_address" class="form-control" cols="30" rows="10"><?php echo $_SESSION['customer']['cust_b_address']; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_107; ?></label>
                                            <input type="text" class="form-control" name="cust_b_city" id="cust_b_city" value="<?php echo $_SESSION['customer']['cust_b_city']; ?>">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_108; ?></label>
                                            <input type="text" class="form-control" name="cust_b_state" id="cust_b_state" value="<?php echo $_SESSION['customer']['cust_b_state']; ?>">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_109; ?></label>
                                            <input type="text" class="form-control" name="cust_b_zip" id="cust_b_zip" value="<?php echo $_SESSION['customer']['cust_b_zip']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="address-card">
                                    <div class="address-section-title">
                                        <h3><?php echo LANG_VALUE_87; ?></h3>
                                        <button type="button" class="copy-btn" onclick="copyToShipping()">
                                            <i class="fa fa-copy"></i> Copy from Profile
                                        </button>
                                        <span class="copy-success" id="shipping-success"><i class="fa fa-check"></i> Copied!</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_102; ?></label>
                                            <input type="text" class="form-control" name="cust_s_name" id="cust_s_name" value="<?php echo $_SESSION['customer']['cust_s_name']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_103; ?></label>
                                            <input type="text" class="form-control" name="cust_s_cname" id="cust_s_cname" value="<?php echo $_SESSION['customer']['cust_s_cname']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_104; ?></label>
                                            <input type="text" class="form-control" name="cust_s_phone" id="cust_s_phone" value="<?php echo $_SESSION['customer']['cust_s_phone']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for=""><?php echo LANG_VALUE_106; ?></label>
                                            <select name="cust_s_country" id="cust_s_country" class="form-control">
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
                                            <label for=""><?php echo LANG_VALUE_105; ?></label>
                                            <textarea name="cust_s_address" id="cust_s_address" class="form-control" cols="30" rows="10"><?php echo $_SESSION['customer']['cust_s_address']; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_107; ?></label>
                                            <input type="text" class="form-control" name="cust_s_city" id="cust_s_city" value="<?php echo $_SESSION['customer']['cust_s_city']; ?>">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_108; ?></label>
                                            <input type="text" class="form-control" name="cust_s_state" id="cust_s_state" value="<?php echo $_SESSION['customer']['cust_s_state']; ?>">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo LANG_VALUE_109; ?></label>
                                            <input type="text" class="form-control" name="cust_s_zip" id="cust_s_zip" value="<?php echo $_SESSION['customer']['cust_s_zip']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 15px;">
                            <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_5; ?>" name="form1">
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<script>
// Profile data from session
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
    
    // Show success message
    var successEl = document.getElementById('billing-success');
    successEl.style.display = 'inline';
    setTimeout(function() { successEl.style.display = 'none'; }, 2000);
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
    
    // Show success message
    var successEl = document.getElementById('shipping-success');
    successEl.style.display = 'inline';
    setTimeout(function() { successEl.style.display = 'none'; }, 2000);
}
</script>

<?php require_once('footer.php'); ?>