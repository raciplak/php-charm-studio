<?php
/**
 * SEO-OPTIMIZED PRODUCT PAGE - 2026 Google Standards
 * 
 * ========== .HTACCESS REWRITE RULES ==========
 * Add these rules to your .htaccess file for clean SEO-friendly URLs:
 * 
 * RewriteEngine On
 * RewriteBase /
 * 
 * # Clean URL: /product/123/product-name → product.php?id=123
 * RewriteRule ^product/([0-9]+)/([a-zA-Z0-9-]+)/?$ product.php?id=$1 [L,QSA]
 * 
 * # Redirect old URLs to new clean URLs (301 permanent)
 * RewriteCond %{QUERY_STRING} ^id=([0-9]+)$
 * RewriteRule ^product\.php$ /product/%1/product-name? [R=301,L]
 * 
 * =============================================
 */

ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Getting all language variables into array as global variable
$i=1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	define('LANG_VALUE_'.$i,$row['lang_value']);
	$i++;
}

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$meta_title_home = $row['meta_title_home'];
    $meta_keyword_home = $row['meta_keyword_home'];
    $meta_description_home = $row['meta_description_home'];
    $before_head = $row['before_head'];
    $after_body = $row['after_body'];
}

// Validate product ID
if(!isset($_REQUEST['id'])) {
    header('location: index.php');
    exit;
} else {
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if( $total == 0 ) {
        header('location: index.php');
        exit;
    }
}

foreach($result as $row) {
    $p_id = $row['p_id'];
    $p_name = $row['p_name'];
    $p_old_price = $row['p_old_price'];
    $p_current_price = $row['p_current_price'];
    $p_qty = $row['p_qty'];
    $p_featured_photo = $row['p_featured_photo'];
    $p_description = $row['p_description'];
    $p_short_description = $row['p_short_description'];
    $p_feature = $row['p_feature'];
    $p_condition = $row['p_condition'];
    $p_return_policy = $row['p_return_policy'];
    $p_total_view = $row['p_total_view'];
    $p_is_featured = $row['p_is_featured'];
    $p_is_active = $row['p_is_active'];
    $ecat_id = $row['ecat_id'];
}

// Getting all categories name for breadcrumb
$statement = $pdo->prepare("SELECT
                        t1.ecat_id,
                        t1.ecat_name,
                        t1.mcat_id,

                        t2.mcat_id,
                        t2.mcat_name,
                        t2.tcat_id,

                        t3.tcat_id,
                        t3.tcat_name

                        FROM tbl_end_category t1
                        JOIN tbl_mid_category t2
                        ON t1.mcat_id = t2.mcat_id
                        JOIN tbl_top_category t3
                        ON t2.tcat_id = t3.tcat_id
                        WHERE t1.ecat_id=?");
$statement->execute(array($ecat_id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $ecat_name = $row['ecat_name'];
    $mcat_id = $row['mcat_id'];
    $mcat_name = $row['mcat_name'];
    $tcat_id = $row['tcat_id'];
    $tcat_name = $row['tcat_name'];
}

// Update view count
$p_total_view = $p_total_view + 1;
$statement = $pdo->prepare("UPDATE tbl_product SET p_total_view=? WHERE p_id=?");
$statement->execute(array($p_total_view,$_REQUEST['id']));

// Get product sizes
$size = array();
$statement = $pdo->prepare("SELECT * FROM tbl_product_size WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $size[] = $row['size_id'];
}

// Get product colors
$color = array();
$statement = $pdo->prepare("SELECT * FROM tbl_product_color WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $color[] = $row['color_id'];
}

// Handle review submission
if(isset($_POST['form_review'])) {
    $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=? AND cust_id=?");
    $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id']));
    $total = $statement->rowCount();
    
    if($total) {
        $error_message = LANG_VALUE_68; 
    } else {
        $statement = $pdo->prepare("INSERT INTO tbl_rating (p_id,cust_id,comment,rating) VALUES (?,?,?,?)");
        $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id'],$_POST['comment'],$_POST['rating']));
        $success_message = LANG_VALUE_163;    
    }
}

// Calculate average rating for this product
$t_rating = 0;
$statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$tot_rating = $statement->rowCount();
$reviews_data = array();
if($tot_rating == 0) {
    $avg_rating = 0;
} else {
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
    foreach ($result as $row) {
        $t_rating = $t_rating + $row['rating'];
        $reviews_data[] = $row;
    }
    $avg_rating = round($t_rating / $tot_rating, 1);
}

// Handle add to cart
if(isset($_POST['form_add_to_cart'])) {
    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
    $statement->execute(array($_REQUEST['id']));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);							
    foreach ($result as $row) {
        $current_p_qty = $row['p_qty'];
    }
    if($_POST['p_qty'] > $current_p_qty):
        $temp_msg = 'Sorry! There are only '.$current_p_qty.' item(s) in stock';
        ?>
        <script type="text/javascript">alert('<?php echo $temp_msg; ?>');</script>
        <?php
    else:
    if(isset($_SESSION['cart_p_id']))
    {
        $arr_cart_p_id = array();
        $arr_cart_size_id = array();
        $arr_cart_color_id = array();
        $arr_cart_p_qty = array();
        $arr_cart_p_current_price = array();

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
        foreach($_SESSION['cart_color_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_color_id[$i] = $value;
        }

        $added = 0;
        if(!isset($_POST['size_id'])) {
            $size_id = 0;
        } else {
            $size_id = $_POST['size_id'];
        }
        if(!isset($_POST['color_id'])) {
            $color_id = 0;
        } else {
            $color_id = $_POST['color_id'];
        }
        for($i=1;$i<=count($arr_cart_p_id);$i++) {
            if( ($arr_cart_p_id[$i]==$_REQUEST['id']) && ($arr_cart_size_id[$i]==$size_id) && ($arr_cart_color_id[$i]==$color_id) ) {
                $added = 1;
                break;
            }
        }
        if($added == 1) {
           $error_message1 = 'This product is already added to the shopping cart.';
        } else {

            $i=0;
            foreach($_SESSION['cart_p_id'] as $key => $res) 
            {
                $i++;
            }
            $new_key = $i+1;

            if(isset($_POST['size_id'])) {
                $size_id = $_POST['size_id'];
                $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
                $statement->execute(array($size_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $size_name = $row['size_name'];
                }
            } else {
                $size_id = 0;
                $size_name = '';
            }
            
            if(isset($_POST['color_id'])) {
                $color_id = $_POST['color_id'];
                $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
                $statement->execute(array($color_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    $color_name = $row['color_name'];
                }
            } else {
                $color_id = 0;
                $color_name = '';
            }

            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_size_id'][$new_key] = $size_id;
            $_SESSION['cart_size_name'][$new_key] = $size_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }
        
    }
    else
    {
        if(isset($_POST['size_id'])) {
            $size_id = $_POST['size_id'];
            $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
            $statement->execute(array($size_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {
                $size_name = $row['size_name'];
            }
        } else {
            $size_id = 0;
            $size_name = '';
        }
        
        if(isset($_POST['color_id'])) {
            $color_id = $_POST['color_id'];
            $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
            $statement->execute(array($color_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {
                $color_name = $row['color_name'];
            }
        } else {
            $color_id = 0;
            $color_name = '';
        }

        $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
        $_SESSION['cart_size_id'][1] = $size_id;
        $_SESSION['cart_size_name'][1] = $size_name;
        $_SESSION['cart_color_id'][1] = $color_id;
        $_SESSION['cart_color_name'][1] = $color_name;
        $_SESSION['cart_p_qty'][1] = $_POST['p_qty'];
        $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
        $_SESSION['cart_p_name'][1] = $_POST['p_name'];
        $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];

        $success_message1 = 'Product is added to the cart successfully!';
    }
    endif;
}

// ========== SEO VARIABLES ==========

// Generate SEO-friendly slug
function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

$product_slug = generateSlug($p_name);

// Canonical URL (clean SEO URL)
$canonical_url = BASE_URL . 'product/' . $p_id . '/' . $product_slug;

// Current URL for comparison
$current_url = BASE_URL . 'product.php?id=' . $p_id;

// Full image URL for Open Graph
$og_image_url = BASE_URL . 'assets/uploads/' . $p_featured_photo;

// SEO Title (max 60 chars, keyword-first)
$seo_title = substr($p_name . ' - Buy ' . $ecat_name . ' | ' . $meta_title_home, 0, 60);

// SEO Meta Description (max 160 chars)
$clean_description = strip_tags($p_short_description ? $p_short_description : $p_description);
$seo_description = substr($p_name . ' for $' . $p_current_price . '. ' . $clean_description, 0, 155) . '...';

// Stock availability for schema
$stock_status = ($p_qty > 0) ? 'InStock' : 'OutOfStock';
$stock_url = 'https://schema.org/' . $stock_status;

// SKU (using product ID if no SKU field exists)
$product_sku = 'SKU-' . str_pad($p_id, 6, '0', STR_PAD_LEFT);

// Get additional product photos for schema
$product_images = array($og_image_url);
$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$photo_result = $statement->fetchAll(PDO::FETCH_ASSOC);
$photo_index = 1;
foreach ($photo_result as $photo_row) {
    $product_images[] = BASE_URL . 'assets/uploads/product_photos/' . $photo_row['photo'];
    $photo_index++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Essential Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Title (60 chars max, keywords first) -->
    <title><?php echo htmlspecialchars($seo_title); ?></title>
    
    <!-- SEO Meta Description (160 chars max) -->
    <meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
    
    <!-- SEO Keywords -->
    <meta name="keywords" content="<?php echo htmlspecialchars($p_name . ', ' . $ecat_name . ', ' . $mcat_name . ', ' . $tcat_name . ', buy online, ' . $meta_keyword_home); ?>">
    
    <!-- Robots -->
    <?php if($p_is_active == 0): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <?php endif; ?>
    
    <!-- Canonical URL (prevents duplicate content) -->
    <link rel="canonical" href="<?php echo $canonical_url; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($p_name); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta property="og:image" content="<?php echo $og_image_url; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($meta_title_home); ?>">
    <meta property="og:locale" content="en_US">
    <meta property="product:price:amount" content="<?php echo $p_current_price; ?>">
    <meta property="product:price:currency" content="USD">
    <meta property="product:availability" content="<?php echo ($p_qty > 0) ? 'in stock' : 'out of stock'; ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($p_name); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta name="twitter:image" content="<?php echo $og_image_url; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/uploads/<?php echo $favicon; ?>">
    
    <!-- Preload Main Product Image (Core Web Vitals - LCP) -->
    <link rel="preload" as="image" href="assets/uploads/<?php echo $p_featured_photo; ?>" fetchpriority="high">
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <!-- DNS Prefetch -->
    <link rel="dns-prefetch" href="//platform-api.sharethis.com">
    
    <!-- Stylesheets (Critical CSS inline for faster FCP) -->
    <style>
        /* Critical CSS for above-the-fold content */
        .product-hero{display:flex;gap:30px;margin-bottom:40px}
        .product-gallery{flex:0 0 45%}
        .product-info{flex:1}
        h1.product-title{font-size:28px;font-weight:700;margin:0 0 15px;color:#222;line-height:1.3}
        .product-price{font-size:26px;font-weight:700;color:#e74c3c}
        .product-price del{color:#999;font-size:18px;margin-right:10px}
        @media(max-width:768px){.product-hero{flex-direction:column}.product-gallery{flex:none}}
    </style>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/tree-menu.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/side-cart.css">
    
    <!-- JSON-LD Structured Data: Product Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "<?php echo htmlspecialchars($p_name, ENT_QUOTES); ?>",
        "description": "<?php echo htmlspecialchars(strip_tags($p_short_description ? $p_short_description : substr($p_description, 0, 500)), ENT_QUOTES); ?>",
        "image": <?php echo json_encode($product_images); ?>,
        "sku": "<?php echo $product_sku; ?>",
        "mpn": "<?php echo $product_sku; ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo htmlspecialchars($meta_title_home, ENT_QUOTES); ?>"
        },
        "category": "<?php echo htmlspecialchars($tcat_name . ' > ' . $mcat_name . ' > ' . $ecat_name, ENT_QUOTES); ?>",
        "url": "<?php echo $canonical_url; ?>",
        "offers": {
            "@type": "Offer",
            "url": "<?php echo $canonical_url; ?>",
            "priceCurrency": "USD",
            "price": "<?php echo $p_current_price; ?>",
            <?php if($p_old_price != ''): ?>
            "priceValidUntil": "<?php echo date('Y-m-d', strtotime('+1 year')); ?>",
            <?php endif; ?>
            "availability": "<?php echo $stock_url; ?>",
            "itemCondition": "https://schema.org/NewCondition",
            "seller": {
                "@type": "Organization",
                "name": "<?php echo htmlspecialchars($meta_title_home, ENT_QUOTES); ?>"
            },
            "shippingDetails": {
                "@type": "OfferShippingDetails",
                "shippingRate": {
                    "@type": "MonetaryAmount",
                    "value": "0",
                    "currency": "USD"
                },
                "shippingDestination": {
                    "@type": "DefinedRegion",
                    "addressCountry": "US"
                },
                "deliveryTime": {
                    "@type": "ShippingDeliveryTime",
                    "handlingTime": {
                        "@type": "QuantitativeValue",
                        "minValue": "1",
                        "maxValue": "2",
                        "unitCode": "DAY"
                    },
                    "transitTime": {
                        "@type": "QuantitativeValue",
                        "minValue": "3",
                        "maxValue": "7",
                        "unitCode": "DAY"
                    }
                }
            }
        }
        <?php if($tot_rating > 0): ?>
        ,"aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo $avg_rating; ?>",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": "<?php echo $tot_rating; ?>",
            "reviewCount": "<?php echo $tot_rating; ?>"
        }
        <?php endif; ?>
        <?php if(count($reviews_data) > 0): ?>
        ,"review": [
            <?php 
            $review_json = array();
            $review_count = 0;
            foreach($reviews_data as $review) {
                if($review_count >= 5) break; // Limit to 5 reviews for schema
                $statement = $pdo->prepare("SELECT cust_name FROM tbl_customer WHERE cust_id=?");
                $statement->execute(array($review['cust_id']));
                $cust = $statement->fetch(PDO::FETCH_ASSOC);
                $reviewer_name = $cust ? $cust['cust_name'] : 'Anonymous';
                
                $review_json[] = '{
                    "@type": "Review",
                    "reviewRating": {
                        "@type": "Rating",
                        "ratingValue": "' . $review['rating'] . '",
                        "bestRating": "5"
                    },
                    "author": {
                        "@type": "Person",
                        "name": "' . htmlspecialchars($reviewer_name, ENT_QUOTES) . '"
                    }' . ($review['comment'] ? ',
                    "reviewBody": "' . htmlspecialchars(substr($review['comment'], 0, 200), ENT_QUOTES) . '"' : '') . '
                }';
                $review_count++;
            }
            echo implode(',', $review_json);
            ?>
        ]
        <?php endif; ?>
    }
    </script>
    
    <!-- JSON-LD Structured Data: Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?php echo BASE_URL; ?>"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "<?php echo htmlspecialchars($tcat_name, ENT_QUOTES); ?>",
                "item": "<?php echo BASE_URL . 'product-category.php?id=' . $tcat_id . '&type=top-category'; ?>"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "<?php echo htmlspecialchars($mcat_name, ENT_QUOTES); ?>",
                "item": "<?php echo BASE_URL . 'product-category.php?id=' . $mcat_id . '&type=mid-category'; ?>"
            },
            {
                "@type": "ListItem",
                "position": 4,
                "name": "<?php echo htmlspecialchars($ecat_name, ENT_QUOTES); ?>",
                "item": "<?php echo BASE_URL . 'product-category.php?id=' . $ecat_id . '&type=end-category'; ?>"
            },
            {
                "@type": "ListItem",
                "position": 5,
                "name": "<?php echo htmlspecialchars($p_name, ENT_QUOTES); ?>",
                "item": "<?php echo $canonical_url; ?>"
            }
        ]
    }
    </script>
    
    <!-- JSON-LD Structured Data: WebPage Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "<?php echo htmlspecialchars($p_name, ENT_QUOTES); ?>",
        "description": "<?php echo htmlspecialchars($seo_description, ENT_QUOTES); ?>",
        "url": "<?php echo $canonical_url; ?>",
        "isPartOf": {
            "@type": "WebSite",
            "name": "<?php echo htmlspecialchars($meta_title_home, ENT_QUOTES); ?>",
            "url": "<?php echo BASE_URL; ?>"
        },
        "primaryImageOfPage": {
            "@type": "ImageObject",
            "url": "<?php echo $og_image_url; ?>"
        },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "<?php echo BASE_URL; ?>"},
                {"@type": "ListItem", "position": 2, "name": "<?php echo htmlspecialchars($tcat_name, ENT_QUOTES); ?>"},
                {"@type": "ListItem", "position": 3, "name": "<?php echo htmlspecialchars($mcat_name, ENT_QUOTES); ?>"},
                {"@type": "ListItem", "position": 4, "name": "<?php echo htmlspecialchars($ecat_name, ENT_QUOTES); ?>"},
                {"@type": "ListItem", "position": 5, "name": "<?php echo htmlspecialchars($p_name, ENT_QUOTES); ?>"}
            ]
        }
    }
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" defer></script>
    <script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons" async></script>
    
    <?php echo $before_head; ?>
</head>
<body itemscope itemtype="https://schema.org/WebPage">

<?php echo $after_body; ?>

<!-- Cart Toast Notification -->
<div id="cart-toast" style="display:none;position:fixed;top:20px;right:20px;z-index:99999;padding:14px 22px;color:#fff;font-size:14px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.2);max-width:320px;">
    <span id="cart-toast-msg"></span>
</div>

<!-- Include header navigation (top bar, logo, menu) -->
<?php 
// Include top bar and navigation from header.php without the <head> section
$statement = $pdo->prepare("SELECT * FROM tbl_social");
$statement->execute();
$social_result = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- top bar -->
<div class="top">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="left">
                    <ul>
                        <li><i class="fa fa-phone"></i> <?php echo $contact_phone; ?></li>
                        <li><i class="fa fa-envelope-o"></i> <?php echo $contact_email; ?></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="right">
                    <ul>
                        <?php foreach ($social_result as $row): ?>
                            <?php if($row['social_url'] != ''): ?>
                            <li><a href="<?php echo $row['social_url']; ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php echo $row['social_name']; ?>"><i class="<?php echo $row['social_icon']; ?>"></i></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="header">
	<div class="container">
		<div class="row inner header-row">
			<div class="col-md-3 logo">
				<a href="index.php"><img src="assets/uploads/<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($meta_title_home); ?> Logo"></a>
			</div>
			
			<div class="col-md-5 search-area-center">
				<form class="header-search-form" role="search" action="search-result.php" method="get">
					<?php $csrf->echoInputField(); ?>
					<div class="header-search-wrapper">
						<input type="text" class="form-control search-top" placeholder="<?php echo LANG_VALUE_2; ?>" name="search_text">
						<button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
					</div>
				</form>
			</div>

			<div class="col-md-4 header-icons">
				<ul>
					<?php
					$header_cart_count = 0;
					$header_cart_total = 0;
					if(isset($_SESSION['cart_p_id'])) {
						$header_cart_count = count($_SESSION['cart_p_id']);
						$i=0;
						foreach($_SESSION['cart_p_qty'] as $key => $value) { $i++; $h_arr_qty[$i] = $value; }
						$i=0;
						foreach($_SESSION['cart_p_current_price'] as $key => $value) { $i++; $h_arr_price[$i] = $value; }
						for($i=1;$i<=$header_cart_count;$i++) { $header_cart_total += $h_arr_price[$i] * $h_arr_qty[$i]; }
					}
					?>
					<?php if(isset($_SESSION['customer'])): ?>
						<li><a href="dashboard.php" class="header-icon-link" title="<?php echo LANG_VALUE_89; ?>"><i class="fa fa-user"></i><span class="icon-label"><?php echo $_SESSION['customer']['cust_name']; ?></span></a></li>
					<?php else: ?>
						<li><a href="login.php" class="header-icon-link" title="<?php echo LANG_VALUE_9; ?>"><i class="fa fa-sign-in"></i><span class="icon-label"><?php echo LANG_VALUE_9; ?></span></a></li>
						<li><a href="registration.php" class="header-icon-link" title="<?php echo LANG_VALUE_15; ?>"><i class="fa fa-user-plus"></i><span class="icon-label"><?php echo LANG_VALUE_15; ?></span></a></li>
					<?php endif; ?>
					<li>
						<a href="javascript:void(0);" onclick="toggleSideCart()" class="header-icon-link cart-trigger" title="Sepet">
							<i class="fa fa-shopping-cart"></i>
							<span class="icon-label"><?php echo LANG_VALUE_1; ?><?php echo $header_cart_total > 0 ? $header_cart_total : '0.00'; ?></span>
							<?php if($header_cart_count > 0): ?>
							<span class="cart-count-badge"><?php echo $header_cart_count; ?></span>
							<?php endif; ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
$statement->execute();
$page_result = $statement->fetchAll(PDO::FETCH_ASSOC);		
foreach ($page_result as $row) {
    $about_title = $row['about_title'];
    $faq_title = $row['faq_title'];
    $contact_title = $row['contact_title'];
}
?>

<nav class="nav" aria-label="Main navigation">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pl_0 pr_0">
                <div class="menu-container">
                    <div class="menu">
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            
                            <?php
                            $statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
                            $statement->execute();
                            $cat_result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($cat_result as $row) {
                                ?>
                                <li><a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category"><?php echo $row['tcat_name']; ?></a>
                                    <ul>
                                        <?php
                                        $statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
                                        $statement1->execute(array($row['tcat_id']));
                                        $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result1 as $row1) {
                                            ?>
                                            <li><a href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category"><?php echo $row1['mcat_name']; ?></a>
                                                <ul>
                                                    <?php
                                                    $statement2 = $pdo->prepare("SELECT * FROM tbl_end_category WHERE mcat_id=?");
                                                    $statement2->execute(array($row1['mcat_id']));
                                                    $result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($result2 as $row2) {
                                                        ?>
                                                        <li><a href="product-category.php?id=<?php echo $row2['ecat_id']; ?>&type=end-category"><?php echo $row2['ecat_name']; ?></a></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            }
                            ?>

                            <li><a href="about.php"><?php echo $about_title; ?></a></li>
                            <li><a href="faq.php"><?php echo $faq_title; ?></a></li>
                            <li><a href="contact.php"><?php echo $contact_title; ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Product Content -->
<main class="page" itemscope itemtype="https://schema.org/Product">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- SEO Breadcrumb with Schema Markup -->
                <nav class="breadcrumb mb_30" aria-label="Breadcrumb">
                    <ol itemscope itemtype="https://schema.org/BreadcrumbList" style="list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:5px;">
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo BASE_URL; ?>"><span itemprop="name">Home</span></a>
                            <meta itemprop="position" content="1">
                        </li>
                        <li aria-hidden="true">&gt;</li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo BASE_URL.'product-category.php?id='.$tcat_id.'&type=top-category' ?>"><span itemprop="name"><?php echo htmlspecialchars($tcat_name); ?></span></a>
                            <meta itemprop="position" content="2">
                        </li>
                        <li aria-hidden="true">&gt;</li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo BASE_URL.'product-category.php?id='.$mcat_id.'&type=mid-category' ?>"><span itemprop="name"><?php echo htmlspecialchars($mcat_name); ?></span></a>
                            <meta itemprop="position" content="3">
                        </li>
                        <li aria-hidden="true">&gt;</li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo BASE_URL.'product-category.php?id='.$ecat_id.'&type=end-category' ?>"><span itemprop="name"><?php echo htmlspecialchars($ecat_name); ?></span></a>
                            <meta itemprop="position" content="4">
                        </li>
                        <li aria-hidden="true">&gt;</li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name"><?php echo htmlspecialchars($p_name); ?></span>
                            <meta itemprop="position" content="5">
                        </li>
                    </ol>
                </nav>

                <article class="product">
                    <div class="row">
                        <!-- Product Gallery -->
                        <div class="col-md-5">
                            <ul class="prod-slider">
                                <!-- Main Featured Image (Eager loaded for LCP) -->
                                <li style="background-image: url(assets/uploads/<?php echo $p_featured_photo; ?>);">
                                    <a class="popup" href="assets/uploads/<?php echo $p_featured_photo; ?>" aria-label="View <?php echo htmlspecialchars($p_name); ?> full size image">
                                        <img src="assets/uploads/<?php echo $p_featured_photo; ?>" 
                                             alt="<?php echo htmlspecialchars($p_name); ?> - Main Product Image" 
                                             itemprop="image"
                                             width="500" 
                                             height="500"
                                             fetchpriority="high"
                                             style="opacity:0;position:absolute;">
                                    </a>
                                </li>
                                <?php
                                $img_index = 2;
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <li style="background-image: url(assets/uploads/product_photos/<?php echo $row['photo']; ?>);">
                                        <a class="popup" href="assets/uploads/product_photos/<?php echo $row['photo']; ?>" aria-label="View <?php echo htmlspecialchars($p_name); ?> image <?php echo $img_index; ?>">
                                            <img src="assets/uploads/product_photos/<?php echo $row['photo']; ?>"
                                                 alt="<?php echo htmlspecialchars($p_name); ?> - Product Photo <?php echo $img_index; ?>" 
                                                 loading="lazy"
                                                 width="500" 
                                                 height="500"
                                                 style="opacity:0;position:absolute;">
                                        </a>
                                    </li>
                                    <?php
                                    $img_index++;
                                }
                                ?>
                            </ul>
                            <div id="prod-pager">
                                <a data-slide-index="0" href="" aria-label="View main image">
                                    <div class="prod-pager-thumb" style="background-image: url(assets/uploads/<?php echo $p_featured_photo; ?>)"></div>
                                </a>
                                <?php
                                $i=1;
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <a data-slide-index="<?php echo $i; ?>" href="" aria-label="View image <?php echo $i+1; ?>">
                                        <div class="prod-pager-thumb" style="background-image: url(assets/uploads/product_photos/<?php echo $row['photo']; ?>)"></div>
                                    </a>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Product Information -->
                        <div class="col-md-7">
                            <!-- H1 Product Title (Critical for SEO) -->
                            <div class="p-title">
                                <h1 itemprop="name" class="product-title" style="font-size:28px;font-weight:700;margin:0 0 15px;color:#222;"><?php echo htmlspecialchars($p_name); ?></h1>
                            </div>
                            
                            <!-- Hidden SEO data -->
                            <meta itemprop="sku" content="<?php echo $product_sku; ?>">
                            <link itemprop="url" href="<?php echo $canonical_url; ?>">
                            
                            <!-- Rating Display -->
                            <div class="p-review" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                                <div class="rating" aria-label="Product rating: <?php echo $avg_rating; ?> out of 5 stars">
                                    <?php
                                    if($avg_rating == 0) {
                                        echo '<span style="color:#999;">No reviews yet</span>';
                                    } else {
                                        for($i=1;$i<=5;$i++) {
                                            if($i <= floor($avg_rating)) {
                                                echo '<i class="fa fa-star" aria-hidden="true"></i>';
                                            } elseif($i == ceil($avg_rating) && $avg_rating != floor($avg_rating)) {
                                                echo '<i class="fa fa-star-half-o" aria-hidden="true"></i>';
                                            } else {
                                                echo '<i class="fa fa-star-o" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo ' <span>(' . $tot_rating . ' reviews)</span>';
                                    }
                                    ?>
                                    <?php if($tot_rating > 0): ?>
                                    <meta itemprop="ratingValue" content="<?php echo $avg_rating; ?>">
                                    <meta itemprop="bestRating" content="5">
                                    <meta itemprop="worstRating" content="1">
                                    <meta itemprop="reviewCount" content="<?php echo $tot_rating; ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Short Description -->
                            <div class="p-short-des" itemprop="description">
                                <p><?php echo $p_short_description; ?></p>
                            </div>
                            
                            <form id="addToCartForm" action="" method="post" onsubmit="return ajaxAddToCart(event)">
                                <style>
                                    .size-selector,.color-selector{display:flex;flex-wrap:wrap;gap:10px;margin-top:8px}
                                    .size-cube{width:45px;height:45px;display:flex;align-items:center;justify-content:center;border:2px solid #ddd;border-radius:0;cursor:pointer;font-weight:600;font-size:14px;background:#fff;transition:all .2s ease}
                                    .size-cube:hover{border-color:#333;background:#f8f8f8}
                                    .size-cube.selected{border-color:#e67e22;background:#e67e22;color:#fff}
                                    .color-cube{width:40px;height:40px;border-radius:0;cursor:pointer;border:3px solid #ddd;transition:all .2s ease;position:relative}
                                    .color-cube:hover{transform:scale(1.1);box-shadow:0 4px 12px rgba(0,0,0,.15)}
                                    .color-cube.selected{border-color:#333;box-shadow:0 0 0 2px #fff,0 0 0 4px #333}
                                    .color-cube.selected::after{content:'✓';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:16px;font-weight:bold;text-shadow:0 1px 2px rgba(0,0,0,.5)}
                                    .selection-label{font-weight:600;margin-bottom:5px;color:#333}
                                </style>
                                
                                <div class="p-options">
                                    <div class="row">
                                        <?php if(!empty($size)): ?>
                                        <div class="col-md-6 col-sm-6 mb_20">
                                            <div class="selection-label"><?php echo LANG_VALUE_52; ?></div>
                                            <input type="hidden" name="size_id" id="selected_size" value="">
                                            <div class="size-selector" role="radiogroup" aria-label="Select size">
                                                <?php
                                                $first_size = true;
                                                $statement = $pdo->prepare("SELECT * FROM tbl_size");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    if(in_array($row['size_id'],$size)) {
                                                        $selected_class = $first_size ? 'selected' : '';
                                                        ?>
                                                        <div class="size-cube <?php echo $selected_class; ?>" 
                                                             data-size-id="<?php echo $row['size_id']; ?>"
                                                             onclick="selectSize(this, <?php echo $row['size_id']; ?>)"
                                                             role="radio"
                                                             aria-checked="<?php echo $first_size ? 'true' : 'false'; ?>"
                                                             tabindex="0">
                                                            <?php echo htmlspecialchars($row['size_name']); ?>
                                                        </div>
                                                        <?php
                                                        if($first_size) {
                                                            echo '<script>document.getElementById("selected_size").value = '.$row['size_id'].';</script>';
                                                            $first_size = false;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if(!empty($color)): ?>
                                        <div class="col-md-6 col-sm-6 mb_20">
                                            <div class="selection-label"><?php echo LANG_VALUE_53; ?></div>
                                            <input type="hidden" name="color_id" id="selected_color" value="">
                                            <div class="color-selector" role="radiogroup" aria-label="Select color">
                                                <?php
                                                $first_color = true;
                                                $statement = $pdo->prepare("SELECT * FROM tbl_color");
                                                $statement->execute();
                                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($result as $row) {
                                                    if(in_array($row['color_id'],$color)) {
                                                        $selected_class = $first_color ? 'selected' : '';
                                                        $color_code = !empty($row['color_code']) ? $row['color_code'] : '#cccccc';
                                                        ?>
                                                        <div class="color-cube <?php echo $selected_class; ?>" 
                                                             style="background-color: <?php echo $color_code; ?>;"
                                                             data-color-id="<?php echo $row['color_id']; ?>"
                                                             title="<?php echo htmlspecialchars($row['color_name']); ?>"
                                                             onclick="selectColor(this, <?php echo $row['color_id']; ?>)"
                                                             role="radio"
                                                             aria-checked="<?php echo $first_color ? 'true' : 'false'; ?>"
                                                             aria-label="<?php echo htmlspecialchars($row['color_name']); ?>"
                                                             tabindex="0">
                                                        </div>
                                                        <?php
                                                        if($first_color) {
                                                            echo '<script>document.getElementById("selected_color").value = '.$row['color_id'].';</script>';
                                                            $first_color = false;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <script>
                                function selectSize(element,sizeId){document.querySelectorAll('.size-cube').forEach(function(cube){cube.classList.remove('selected');cube.setAttribute('aria-checked','false');});element.classList.add('selected');element.setAttribute('aria-checked','true');document.getElementById('selected_size').value=sizeId;}
                                function selectColor(element,colorId){document.querySelectorAll('.color-cube').forEach(function(cube){cube.classList.remove('selected');cube.setAttribute('aria-checked','false');});element.classList.add('selected');element.setAttribute('aria-checked','true');document.getElementById('selected_color').value=colorId;}
                                </script>
                                
                                <!-- Price with Schema -->
                                <div class="p-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                    <span style="font-size:14px;"><?php echo LANG_VALUE_54; ?></span><br>
                                    <span class="product-price">
                                        <?php if($p_old_price!=''): ?>
                                            <del><?php echo LANG_VALUE_1; ?><?php echo $p_old_price; ?></del>
                                        <?php endif; ?> 
                                        <span itemprop="price" content="<?php echo $p_current_price; ?>"><?php echo LANG_VALUE_1; ?><?php echo $p_current_price; ?></span>
                                    </span>
                                    <meta itemprop="priceCurrency" content="USD">
                                    <link itemprop="availability" href="<?php echo $stock_url; ?>">
                                    <link itemprop="itemCondition" href="https://schema.org/NewCondition">
                                    <link itemprop="url" href="<?php echo $canonical_url; ?>">
                                    <?php if($p_old_price != ''): ?>
                                    <meta itemprop="priceValidUntil" content="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <input type="hidden" name="p_current_price" value="<?php echo $p_current_price; ?>">
                                <input type="hidden" name="p_name" value="<?php echo htmlspecialchars($p_name); ?>">
                                <input type="hidden" name="p_featured_photo" value="<?php echo $p_featured_photo; ?>">
                                
                                <div class="p-quantity-wrapper" style="display:flex;align-items:flex-end;gap:15px;flex-wrap:wrap;">
                                    <div class="p-quantity">
                                        <label for="p_qty"><?php echo LANG_VALUE_55; ?></label><br>
                                        <input type="number" class="input-text qty" step="1" min="1" max="<?php echo $p_qty; ?>" name="p_qty" id="p_qty" value="1" title="Quantity" size="4" pattern="[0-9]*" inputmode="numeric" style="width:80px;" aria-label="Product quantity">
                                    </div>
                                    <div class="btn-cart btn-cart1" style="margin-top:0;">
                                        <input type="submit" value="<?php echo LANG_VALUE_154; ?>" name="form_add_to_cart" <?php echo ($p_qty <= 0) ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                                
                                <!-- Stock Status -->
                                <div style="margin-top:10px;">
                                    <?php if($p_qty > 0): ?>
                                        <span style="color:#28a745;font-weight:600;"><i class="fa fa-check-circle"></i> In Stock (<?php echo $p_qty; ?> available)</span>
                                    <?php else: ?>
                                        <span style="color:#dc3545;font-weight:600;"><i class="fa fa-times-circle"></i> Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Fast Shipping Badge -->
                                <div class="fast-shipping" style="display:flex;align-items:center;gap:10px;margin-top:15px;padding:10px 15px;background-color:#f8f9fa;border-radius:0;border-left:3px solid #28a745;">
                                    <i class="fa fa-truck" style="font-size:24px;color:#28a745;" aria-hidden="true"></i>
                                    <div>
                                        <strong style="color:#28a745;">Fast Shipping</strong><br>
                                        <span style="font-size:12px;color:#666;">Free delivery on orders over $50</span>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Social Sharing -->
                            <div class="share">
                                <?php echo LANG_VALUE_58; ?><br>
                                <div class="sharethis-inline-share-buttons"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Tabs -->
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?php echo LANG_VALUE_59; ?></a></li>
                                <li role="presentation"><a href="#feature" aria-controls="feature" role="tab" data-toggle="tab"><?php echo LANG_VALUE_60; ?></a></li>
                                <li role="presentation"><a href="#condition" aria-controls="condition" role="tab" data-toggle="tab"><?php echo LANG_VALUE_61; ?></a></li>
                                <li role="presentation"><a href="#return_policy" aria-controls="return_policy" role="tab" data-toggle="tab"><?php echo LANG_VALUE_62; ?></a></li>
                            </ul>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="description" style="margin-top:-30px;">
                                    <div itemprop="description">
                                        <?php echo ($p_description == '') ? LANG_VALUE_70 : $p_description; ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="feature" style="margin-top:-30px;">
                                    <?php echo ($p_feature == '') ? LANG_VALUE_71 : $p_feature; ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="condition" style="margin-top:-30px;">
                                    <?php echo ($p_condition == '') ? LANG_VALUE_72 : $p_condition; ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="return_policy" style="margin-top:-30px;">
                                    <?php echo ($p_return_policy == '') ? LANG_VALUE_73 : $p_return_policy; ?>
                                </div>
                                
                                <!-- Reviews Section -->
                                <div role="tabpanel" class="tab-pane" id="review" style="margin-top:-30px;">
                                    <div class="review-form">
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_rating t1 JOIN tbl_customer t2 ON t1.cust_id = t2.cust_id WHERE t1.p_id=?");
                                        $statement->execute(array($_REQUEST['id']));
                                        $total = $statement->rowCount();
                                        ?>
                                        <h2><?php echo LANG_VALUE_63; ?> (<?php echo $total; ?>)</h2>
                                        <?php
                                        if($total) {
                                            $j=0;
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                $j++;
                                                ?>
                                                <div class="mb_10" itemprop="review" itemscope itemtype="https://schema.org/Review">
                                                    <b><u><?php echo LANG_VALUE_64; ?> <?php echo $j; ?></u></b>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th style="width:170px;"><?php echo LANG_VALUE_75; ?></th>
                                                            <td itemprop="author" itemscope itemtype="https://schema.org/Person"><span itemprop="name"><?php echo htmlspecialchars($row['cust_name']); ?></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?php echo LANG_VALUE_76; ?></th>
                                                            <td itemprop="reviewBody"><?php echo htmlspecialchars($row['comment']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th><?php echo LANG_VALUE_78; ?></th>
                                                            <td>
                                                                <div class="rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                                                    <meta itemprop="ratingValue" content="<?php echo $row['rating']; ?>">
                                                                    <meta itemprop="bestRating" content="5">
                                                                    <?php
                                                                    for($i=1;$i<=5;$i++) {
                                                                        if($i>$row['rating']) {
                                                                            echo '<i class="fa fa-star-o" aria-hidden="true"></i>';
                                                                        } else {
                                                                            echo '<i class="fa fa-star" aria-hidden="true"></i>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            echo LANG_VALUE_74;
                                        }
                                        ?>
                                        
                                        <h2><?php echo LANG_VALUE_65; ?></h2>
                                        <?php
                                        if($error_message != '') {
                                            echo "<script>alert('".$error_message."')</script>";
                                        }
                                        if($success_message != '') {
                                            echo "<script>alert('".$success_message."')</script>";
                                        }
                                        ?>
                                        <?php if(isset($_SESSION['customer'])): ?>
                                            <?php
                                            $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=? AND cust_id=?");
                                            $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id']));
                                            $total = $statement->rowCount();
                                            ?>
                                            <?php if($total==0): ?>
                                            <form action="" method="post">
                                            <div class="rating-section">
                                                <input type="radio" name="rating" class="rating" value="1" checked>
                                                <input type="radio" name="rating" class="rating" value="2" checked>
                                                <input type="radio" name="rating" class="rating" value="3" checked>
                                                <input type="radio" name="rating" class="rating" value="4" checked>
                                                <input type="radio" name="rating" class="rating" value="5" checked>
                                            </div>                                            
                                            <div class="form-group">
                                                <textarea name="comment" class="form-control" cols="30" rows="10" placeholder="Write your comment (optional)" style="height:100px;"></textarea>
                                            </div>
                                            <input type="submit" class="btn btn-default" name="form_review" value="<?php echo LANG_VALUE_67; ?>">
                                            </form>
                                            <?php else: ?>
                                                <span style="color:red;"><?php echo LANG_VALUE_68; ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p class="error">
                                                <?php echo LANG_VALUE_69; ?><br>
                                                <a href="login.php" style="color:red;text-decoration:underline;"><?php echo LANG_VALUE_9; ?></a>
                                            </p>
                                        <?php endif; ?>                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>

<!-- Related Products Section -->
<section class="product bg-gray pt_70 pb_70" aria-label="Related Products">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo LANG_VALUE_155; ?></h2>
                    <h3><?php echo LANG_VALUE_156; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="product-carousel" itemscope itemtype="https://schema.org/ItemList">
                    <meta itemprop="name" content="Related Products">
                    <?php
                    $related_position = 1;
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE ecat_id=? AND p_id!=? AND p_is_active=1 LIMIT 8");
                    $statement->execute(array($ecat_id,$_REQUEST['id']));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                        // Calculate rating for related product
                        $t_rating = 0;
                        $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                        $statement1->execute(array($row['p_id']));
                        $tot_rating = $statement1->rowCount();
                        if($tot_rating == 0) {
                            $rel_avg_rating = 0;
                        } else {
                            $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result1 as $row1) {
                                $t_rating = $t_rating + $row1['rating'];
                            }
                            $rel_avg_rating = $t_rating / $tot_rating;
                        }
                        ?>
                        <div class="item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <meta itemprop="position" content="<?php echo $related_position; ?>">
                            <div class="thumb" itemprop="item" itemscope itemtype="https://schema.org/Product">
                                <div class="photo" style="background-image:url(assets/uploads/<?php echo $row['p_featured_photo']; ?>);">
                                    <img src="assets/uploads/<?php echo $row['p_featured_photo']; ?>" 
                                         alt="<?php echo htmlspecialchars($row['p_name']); ?> - Related Product" 
                                         loading="lazy"
                                         width="300"
                                         height="300"
                                         style="opacity:0;position:absolute;"
                                         itemprop="image">
                                </div>
                                <div class="overlay"></div>
                                <link itemprop="url" href="<?php echo BASE_URL; ?>product.php?id=<?php echo $row['p_id']; ?>">
                                <meta itemprop="name" content="<?php echo htmlspecialchars($row['p_name']); ?>">
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo htmlspecialchars($row['p_name']); ?></a></h3>
                                <h4>
                                    <?php echo LANG_VALUE_1; ?><?php echo $row['p_current_price']; ?> 
                                    <?php if($row['p_old_price'] != ''): ?>
                                    <del><?php echo LANG_VALUE_1; ?><?php echo $row['p_old_price']; ?></del>
                                    <?php endif; ?>
                                </h4>
                                <div class="rating" aria-label="Rating: <?php echo round($rel_avg_rating, 1); ?> out of 5">
                                    <?php
                                    if($rel_avg_rating > 0) {
                                        for($i=1;$i<=5;$i++) {
                                            if($i <= floor($rel_avg_rating)) {
                                                echo '<i class="fa fa-star" aria-hidden="true"></i>';
                                            } elseif($i == ceil($rel_avg_rating) && $rel_avg_rating != floor($rel_avg_rating)) {
                                                echo '<i class="fa fa-star-half-o" aria-hidden="true"></i>';
                                            } else {
                                                echo '<i class="fa fa-star-o" aria-hidden="true"></i>';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo LANG_VALUE_154; ?></a></p>
                            </div>
                        </div>
                        <?php
                        $related_position++;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function showCartToast(msg, isSuccess) {
    var toast = document.getElementById('cart-toast');
    var msgEl = document.getElementById('cart-toast-msg');
    msgEl.textContent = msg;
    toast.style.background = isSuccess ? '#28a745' : '#dc3545';
    toast.style.display = 'block';
    toast.style.opacity = '1';
    setTimeout(function() {
        toast.style.opacity = '0';
        setTimeout(function() { toast.style.display = 'none'; }, 300);
    }, 3000);
}

function ajaxAddToCart(e) {
    e.preventDefault();
    var form = document.getElementById('addToCartForm');
    var formData = new FormData();
    formData.append('p_id', '<?php echo $p_id; ?>');
    formData.append('p_qty', form.querySelector('[name="p_qty"]').value);
    formData.append('p_current_price', form.querySelector('[name="p_current_price"]').value);
    formData.append('p_name', form.querySelector('[name="p_name"]').value);
    formData.append('p_featured_photo', form.querySelector('[name="p_featured_photo"]').value);
    
    var sizeEl = form.querySelector('[name="size_id"]');
    var colorEl = form.querySelector('[name="color_id"]');
    if(sizeEl) formData.append('size_id', sizeEl.value);
    if(colorEl) formData.append('color_id', colorEl.value);

    var btn = form.querySelector('[name="form_add_to_cart"]');
    btn.disabled = true;
    btn.value = '...';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart-add-ajax.php', true);
    xhr.onload = function() {
        if(xhr.status === 200) {
            try {
                var res = JSON.parse(xhr.responseText);
                showCartToast(res.message, res.status === 'success');
                if(res.status === 'success') {
                    // Update all cart count badges
                    var badges = document.querySelectorAll('.cart-count-badge');
                    badges.forEach(function(b) { b.textContent = res.cart_count; });
                    // If no badge exists, create one
                    if(badges.length === 0) {
                        var cartLink = document.querySelector('.cart-trigger');
                        if(cartLink) {
                            var badge = document.createElement('span');
                            badge.className = 'cart-count-badge';
                            badge.textContent = res.cart_count;
                            cartLink.appendChild(badge);
                        }
                    }
                    // Update cart total text
                    var iconLabels = document.querySelectorAll('.cart-trigger .icon-label');
                    iconLabels.forEach(function(l) {
                        l.textContent = '<?php echo LANG_VALUE_1; ?>' + res.cart_total;
                    });
                    btn.value = '✓ Eklendi';
                    setTimeout(function() { btn.value = '<?php echo LANG_VALUE_154; ?>'; btn.disabled = false; }, 2000);
                } else {
                    btn.value = '<?php echo LANG_VALUE_154; ?>';
                    btn.disabled = false;
                }
            } catch(ex) {
                showCartToast('Bir hata oluştu', false);
                btn.value = '<?php echo LANG_VALUE_154; ?>';
                btn.disabled = false;
            }
        }
    };
    xhr.onerror = function() {
        showCartToast('Bağlantı hatası', false);
        btn.value = '<?php echo LANG_VALUE_154; ?>';
        btn.disabled = false;
    };
    xhr.send(formData);
    return false;
}
</script>

<?php require_once('footer.php'); ?>
