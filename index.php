<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
    $cta_title = $row['cta_title'];
    $cta_content = $row['cta_content'];
    $cta_read_more_text = $row['cta_read_more_text'];
    $cta_read_more_url = $row['cta_read_more_url'];
    $cta_photo = $row['cta_photo'];
    $featured_product_title = $row['featured_product_title'];
    $featured_product_subtitle = $row['featured_product_subtitle'];
    $latest_product_title = $row['latest_product_title'];
    $latest_product_subtitle = $row['latest_product_subtitle'];
    $popular_product_title = $row['popular_product_title'];
    $popular_product_subtitle = $row['popular_product_subtitle'];
    $total_featured_product_home = $row['total_featured_product_home'];
    $total_latest_product_home = $row['total_latest_product_home'];
    $total_popular_product_home = $row['total_popular_product_home'];
    $home_service_on_off = $row['home_service_on_off'];
    $home_welcome_on_off = $row['home_welcome_on_off'];
    $home_featured_product_on_off = $row['home_featured_product_on_off'];
    $home_latest_product_on_off = $row['home_latest_product_on_off'];
    $home_popular_product_on_off = $row['home_popular_product_on_off'];
    $slider_display_mode = isset($row['slider_display_mode']) ? $row['slider_display_mode'] : 'slider';
}
?>

<?php if($slider_display_mode == 'cube'): ?>
<!-- Flipping Cubes Mode -->
<style>
.cube-slider-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 450px;
    width: 100%;
    overflow: hidden;
}

.flip-cube {
    width: 100%;
    height: 320px;
    perspective: 1000px;
    cursor: pointer;
}

.flip-cube-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform-style: preserve-3d;
    animation: cubeFlip 8s ease-in-out infinite;
}

.flip-cube:nth-child(1) .flip-cube-inner { animation-delay: 0s; }
.flip-cube:nth-child(2) .flip-cube-inner { animation-delay: 2s; }
.flip-cube:nth-child(3) .flip-cube-inner { animation-delay: 4s; }
.flip-cube:nth-child(4) .flip-cube-inner { animation-delay: 6s; }

@keyframes cubeFlip {
    0%, 45% { transform: rotateY(0deg); }
    50%, 95% { transform: rotateY(180deg); }
    100% { transform: rotateY(360deg); }
}

/* Pause animation on hover */
.flip-cube:hover .flip-cube-inner {
    animation-play-state: paused;
}

.flip-cube-front, .flip-cube-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 0;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.3), 0 5px 15px rgba(0,0,0,0.2);
}

.flip-cube-front {
    background-size: cover;
    background-position: center;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.flip-cube-front::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
}

.flip-cube-content {
    position: relative;
    z-index: 2;
    padding: 25px;
    color: white;
    text-align: center;
}

.flip-cube-content h2.cube-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    line-height: 1.3;
}

.flip-cube-content p {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 15px;
    line-height: 1.4;
}

.flip-cube-content .cube-btn {
    display: inline-block;
    padding: 10px 25px;
    background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
    color: white;
    text-decoration: none;
    border-radius: 0;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(233, 69, 96, 0.4);
}

.flip-cube-content .cube-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(233, 69, 96, 0.6);
}

.flip-cube-back {
    background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%);
    transform: rotateY(180deg);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
}

.flip-cube-back-content {
    text-align: center;
    color: white;
}

.flip-cube-back-content i {
    font-size: 50px;
    margin-bottom: 15px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.flip-cube-back-content h4 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 10px;
}

.flip-cube-back-content p {
    font-size: 14px;
    opacity: 0.9;
}

/* Sparkle effect */
.flip-cube::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: sparkle 4s linear infinite;
    pointer-events: none;
}

@keyframes sparkle {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 1200px) {
    .cube-slider-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 900px) {
    .cube-slider-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .cube-slider-container {
        grid-template-columns: 1fr;
        padding: 30px 15px;
    }
    .flip-cube {
        height: 300px;
    }
}
</style>

<!-- SEO: Hero Section with Semantic Structure -->
<section class="cube-slider-section" aria-label="Featured Promotions" itemscope itemtype="https://schema.org/ItemList">
    <meta itemprop="name" content="Featured Promotions and Deals">
    <meta itemprop="description" content="Discover our latest promotions, special offers, and featured products">
    <div class="cube-slider-container" role="region" aria-label="Interactive product showcase">
        <?php
        $cube_position = 0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider WHERE is_active = 1 ORDER BY position ASC");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        foreach ($result as $row) {
            $cube_position++;
            $slider_title = htmlspecialchars($row['heading'], ENT_QUOTES, 'UTF-8');
            $slider_desc = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
            $slider_image = 'assets/uploads/' . $row['photo'];
        ?>
        <article class="flip-cube" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-label="<?php echo $slider_title; ?>">
            <meta itemprop="position" content="<?php echo $cube_position; ?>">
            <div class="flip-cube-inner">
                <div class="flip-cube-front" style="background-image:url(<?php echo $slider_image; ?>);" role="img" aria-label="<?php echo $slider_title; ?> - promotional image">
                    <img src="<?php echo $slider_image; ?>" alt="<?php echo $slider_title; ?> - <?php echo substr($slider_desc, 0, 100); ?>" loading="lazy" style="display:none;" itemprop="image">
                    <div class="flip-cube-content">
                        <h2 itemprop="name" class="cube-title"><?php echo $slider_title; ?></h2>
                        <p itemprop="description"><?php echo substr($slider_desc, 0, 80); ?>...</p>
                        <a href="<?php echo htmlspecialchars($row['button_url'], ENT_QUOTES, 'UTF-8'); ?>" class="cube-btn" itemprop="url" title="<?php echo htmlspecialchars($row['button_text'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo $slider_title; ?>"><?php echo htmlspecialchars($row['button_text'], ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
                </div>
                <div class="flip-cube-back" aria-hidden="true">
                    <div class="flip-cube-back-content">
                        <i class="fa fa-hand-pointer-o" aria-hidden="true"></i>
                        <h3>Daha Fazla Keşfet</h3>
                        <p>Harika fırsatları keşfetmek için tıklayın!</p>
                    </div>
                </div>
            </div>
        </article>
        <?php } ?>
    </div>
</section>

<?php else: ?>
<!-- Normal Slider Mode - SEO Optimized -->
<section class="hero-slider-section" aria-label="Featured Promotions" itemscope itemtype="https://schema.org/ItemList">
    <meta itemprop="name" content="Featured Promotions and Special Offers">
    <meta itemprop="description" content="Browse our latest promotions, special deals, and featured products">
    
    <div id="bootstrap-touch-slider" class="carousel bs-slider fade control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="5000" role="region" aria-roledescription="carousel" aria-label="Hero promotions slider">

        <!-- Indicators -->
        <ol class="carousel-indicators" role="tablist">
            <?php
            $i=0;
            $statement = $pdo->prepare("SELECT * FROM tbl_slider WHERE is_active = 1 ORDER BY position ASC");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {            
                ?>
                <li data-target="#bootstrap-touch-slider" data-slide-to="<?php echo $i; ?>" <?php if($i==0) {echo 'class="active"';} ?> role="tab" aria-label="Slide <?php echo ($i+1); ?>: <?php echo htmlspecialchars($row['heading'], ENT_QUOTES, 'UTF-8'); ?>"></li>
                <?php
                $i++;
            }
            ?>
        </ol>

        <!-- Wrapper For Slides -->
        <div class="carousel-inner" role="group" aria-live="polite">

            <?php
            $i=0;
            $slide_position = 0;
            $statement = $pdo->prepare("SELECT * FROM tbl_slider WHERE is_active = 1 ORDER BY position ASC");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
            foreach ($result as $row) {
                $slide_position++;
                $slide_title = htmlspecialchars($row['heading'], ENT_QUOTES, 'UTF-8');
                $slide_desc = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
                $slide_image = 'assets/uploads/' . $row['photo'];
                $slide_btn_text = htmlspecialchars($row['button_text'], ENT_QUOTES, 'UTF-8');
                $slide_btn_url = htmlspecialchars($row['button_url'], ENT_QUOTES, 'UTF-8');
                ?>
                <article class="item <?php if($i==0) {echo 'active';} ?>" style="background-image:url(<?php echo $slide_image; ?>);" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" role="tabpanel" aria-label="<?php echo $slide_title; ?>">
                    <meta itemprop="position" content="<?php echo $slide_position; ?>">
                    <img src="<?php echo $slide_image; ?>" alt="<?php echo $slide_title; ?> - <?php echo substr($slide_desc, 0, 100); ?>" loading="<?php echo ($i==0) ? 'eager' : 'lazy'; ?>" style="display:none;" itemprop="image">
                    <div class="bs-slider-overlay"></div>
                    <div class="container">
                        <div class="row">
                            <div class="slide-text <?php if($row['position'] == 'Left') {echo 'slide_style_left';} elseif($row['position'] == 'Center') {echo 'slide_style_center';} elseif($row['position'] == 'Right') {echo 'slide_style_right';} ?>">
                                <h2 itemprop="name" data-animation="animated <?php if($row['position'] == 'Left') {echo 'zoomInLeft';} elseif($row['position'] == 'Center') {echo 'flipInX';} elseif($row['position'] == 'Right') {echo 'zoomInRight';} ?>"><?php echo $slide_title; ?></h2>
                                <p itemprop="description" data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Center') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>"><?php echo nl2br($slide_desc); ?></p>
                                <a href="<?php echo $slide_btn_url; ?>" itemprop="url" title="<?php echo $slide_btn_text; ?> - <?php echo $slide_title; ?>" class="btn btn-primary" data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Center') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>"><?php echo $slide_btn_text; ?></a>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
                $i++;
            }
            ?>
        </div>

    <!-- Slider Left Control -->
    <a class="left carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="prev">
        <span class="fa fa-angle-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>

    <!-- Slider Right Control -->
    <a class="right carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="next">
            <span class="fa fa-angle-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>

    </div>
</section>
<?php endif; ?>


<?php if($home_service_on_off == 1): ?>
<div class="service bg-gray">
    <div class="container">
        <div class="row">
            <?php
                $statement = $pdo->prepare("SELECT * FROM tbl_service");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                foreach ($result as $row) {
                    ?>
                    <div class="col-md-4">
                        <div class="item">
                            <div class="photo"><img src="assets/uploads/<?php echo $row['photo']; ?>" width="150px" alt="<?php echo $row['title']; ?>"></div>
                            <h3><?php echo $row['title']; ?></h3>
                            <p>
                                <?php echo nl2br($row['content']); ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Category Banners Section - Fancy Cube Cards -->
<?php
$statement = $pdo->prepare("SELECT * FROM tbl_category_banner WHERE is_active = 1 ORDER BY display_order ASC");
$statement->execute();
$category_banners = $statement->fetchAll(PDO::FETCH_ASSOC);
if(count($category_banners) > 0):
?>
<style>
.category-banners-section {
    padding: 50px 0;
    background: #ffffff;
    position: relative;
    overflow: hidden;
}

.category-banners-section .section-header {
    text-align: left;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    padding: 0 20px;
}

.category-banners-section .section-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 0;
    text-transform: none;
    letter-spacing: 0;
}

.category-banner-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    padding: 0 20px;
    position: relative;
    z-index: 2;
    max-width: 1400px;
    margin: 0 auto;
}

.category-banner-card {
    position: relative;
    width: 100%;
    height: 180px;
    cursor: pointer;
    overflow: hidden;
}

.category-banner-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.3s ease;
}

.category-banner-card:hover .category-banner-inner {
    transform: scale(1.02);
}

.category-banner-face {
    position: absolute;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    border-radius: 0;
    overflow: hidden;
}

.category-banner-face::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        180deg,
        transparent 0%,
        transparent 50%,
        rgba(0,0,0,0.4) 100%
    );
    transition: all 0.3s ease;
}

.category-banner-card:hover .category-banner-face::before {
    background: linear-gradient(
        180deg,
        transparent 0%,
        transparent 40%,
        rgba(0,0,0,0.5) 100%
    );
}

.category-banner-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    z-index: 3;
}

.category-banner-title {
    font-size: 28px;
    font-weight: 900;
    color: white;
    margin-bottom: 5px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    line-height: 1.1;
    font-style: italic;
    text-transform: uppercase;
    letter-spacing: -1px;
}

.category-banner-tag {
    display: inline-block;
    padding: 4px 12px;
    background: #4CAF50;
    color: white;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Responsive */
@media (max-width: 900px) {
    .category-banner-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .category-banner-card {
        height: 200px;
    }
}

@media (max-width: 600px) {
    .category-banners-section {
        padding: 40px 0;
    }
    .category-banner-grid {
        grid-template-columns: 1fr;
    }
    .category-banner-card {
        height: 180px;
    }
    }
    .category-banners-section .section-header h2 {
        font-size: 28px;
    }
}
</style>

<section class="category-banners-section" aria-label="Category Banners" itemscope itemtype="https://schema.org/ItemList">
    <meta itemprop="name" content="Shop By Category">
    <meta itemprop="description" content="Explore our product categories and find exactly what you're looking for">
    
    <div class="section-header">
        <h2>Kategori Bannerları</h2>
    </div>
    
    <div class="category-banner-grid" role="region" aria-label="Category showcase">
        <?php
        $banner_position = 0;
        foreach ($category_banners as $banner) {
            $banner_position++;
            $banner_title = htmlspecialchars($banner['title'], ENT_QUOTES, 'UTF-8');
            $banner_subtitle = htmlspecialchars($banner['subtitle'], ENT_QUOTES, 'UTF-8');
            $banner_image = 'assets/uploads/' . $banner['photo'];
            $banner_btn_url = htmlspecialchars($banner['button_url'], ENT_QUOTES, 'UTF-8');
        ?>
        <a href="<?php echo $banner_btn_url; ?>" class="category-banner-card" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" title="<?php echo $banner_title; ?> - <?php echo $banner_subtitle; ?>">
            <meta itemprop="position" content="<?php echo $banner_position; ?>">
            <div class="category-banner-inner">
                <div class="category-banner-face" style="background-image: url(<?php echo $banner_image; ?>);" role="img" aria-label="<?php echo $banner_title; ?>">
                    <img src="<?php echo $banner_image; ?>" alt="<?php echo $banner_title; ?> - <?php echo substr($banner_subtitle, 0, 80); ?>" loading="lazy" style="display:none;" itemprop="image">
                    <div class="category-banner-content">
                        <h3 class="category-banner-title" itemprop="name"><?php echo $banner_title; ?></h3>
                        <span class="category-banner-tag" itemprop="description"><?php echo $banner_subtitle; ?></span>
                    </div>
                </div>
            </div>
        </a>
        <?php } ?>
    </div>
</section>
<?php endif; ?>

<?php if($home_featured_product_on_off == 1): ?>
<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $featured_product_title; ?></h2>
                    <h3><?php echo $featured_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">
                    
                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_featured=? AND p_is_active=? LIMIT ".$total_featured_product_home);
                    $statement->execute(array(1,1));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                    foreach ($result as $row) {
                        ?>
                        <div class="item">
                            <div class="thumb">
                                <div class="photo" style="background-image:url(assets/uploads/<?php echo $row['p_featured_photo']; ?>);"></div>
                                <div class="overlay"></div>
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                <h4>
                                    $<?php echo $row['p_current_price']; ?> 
                                    <?php if($row['p_old_price'] != ''): ?>
                                    <del>
                                        $<?php echo $row['p_old_price']; ?>
                                    </del>
                                    <?php endif; ?>
                                </h4>
                                <div class="rating">
                                    <?php
                                    $t_rating = 0;
                                    $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                                    $statement1->execute(array($row['p_id']));
                                    $tot_rating = $statement1->rowCount();
                                    if($tot_rating == 0) {
                                        $avg_rating = 0;
                                    } else {
                                        $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result1 as $row1) {
                                            $t_rating = $t_rating + $row1['rating'];
                                        }
                                        $avg_rating = $t_rating / $tot_rating;
                                    }
                                    ?>
                                    <?php
                                    if($avg_rating == 0) {
                                        echo '';
                                    }
                                    elseif($avg_rating == 1.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    } 
                                    elseif($avg_rating == 2.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 3.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 4.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                        ';
                                    }
                                    else {
                                        for($i=1;$i<=5;$i++) {
                                            ?>
                                            <?php if($i>$avg_rating): ?>
                                                <i class="fa fa-star-o"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star"></i>
                                            <?php endif; ?>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>

                                <?php if($row['p_qty'] == 0): ?>
                                    <div class="out-of-stock">
                                        <div class="inner">
                                            Stokta Yok
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-shopping-cart"></i> Sepete Ekle</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if($home_latest_product_on_off == 1): ?>
<div class="product bg-gray pt_70 pb_30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $latest_product_title; ?></h2>
                    <h3><?php echo $latest_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_active=? ORDER BY p_id DESC LIMIT ".$total_latest_product_home);
                    $statement->execute(array(1));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                    foreach ($result as $row) {
                        ?>
                        <div class="item">
                            <div class="thumb">
                                <div class="photo" style="background-image:url(assets/uploads/<?php echo $row['p_featured_photo']; ?>);"></div>
                                <div class="overlay"></div>
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                <h4>
                                    $<?php echo $row['p_current_price']; ?> 
                                    <?php if($row['p_old_price'] != ''): ?>
                                    <del>
                                        $<?php echo $row['p_old_price']; ?>
                                    </del>
                                    <?php endif; ?>
                                </h4>
                                <div class="rating">
                                    <?php
                                    $t_rating = 0;
                                    $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                                    $statement1->execute(array($row['p_id']));
                                    $tot_rating = $statement1->rowCount();
                                    if($tot_rating == 0) {
                                        $avg_rating = 0;
                                    } else {
                                        $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result1 as $row1) {
                                            $t_rating = $t_rating + $row1['rating'];
                                        }
                                        $avg_rating = $t_rating / $tot_rating;
                                    }
                                    ?>
                                    <?php
                                    if($avg_rating == 0) {
                                        echo '';
                                    }
                                    elseif($avg_rating == 1.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    } 
                                    elseif($avg_rating == 2.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 3.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 4.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                        ';
                                    }
                                    else {
                                        for($i=1;$i<=5;$i++) {
                                            ?>
                                            <?php if($i>$avg_rating): ?>
                                                <i class="fa fa-star-o"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star"></i>
                                            <?php endif; ?>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php if($row['p_qty'] == 0): ?>
                                    <div class="out-of-stock">
                                        <div class="inner">
                                            Stokta Yok
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-shopping-cart"></i> Sepete Ekle</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>


            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if($home_popular_product_on_off == 1): ?>
<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $popular_product_title; ?></h2>
                    <h3><?php echo $popular_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_active=? ORDER BY p_total_view DESC LIMIT ".$total_popular_product_home);
                    $statement->execute(array(1));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
                    foreach ($result as $row) {
                        ?>
                        <div class="item">
                            <div class="thumb">
                                <div class="photo" style="background-image:url(assets/uploads/<?php echo $row['p_featured_photo']; ?>);"></div>
                                <div class="overlay"></div>
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                <h4>
                                    $<?php echo $row['p_current_price']; ?> 
                                    <?php if($row['p_old_price'] != ''): ?>
                                    <del>
                                        $<?php echo $row['p_old_price']; ?>
                                    </del>
                                    <?php endif; ?>
                                </h4>
                                <div class="rating">
                                    <?php
                                    $t_rating = 0;
                                    $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                                    $statement1->execute(array($row['p_id']));
                                    $tot_rating = $statement1->rowCount();
                                    if($tot_rating == 0) {
                                        $avg_rating = 0;
                                    } else {
                                        $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result1 as $row1) {
                                            $t_rating = $t_rating + $row1['rating'];
                                        }
                                        $avg_rating = $t_rating / $tot_rating;
                                    }
                                    ?>
                                    <?php
                                    if($avg_rating == 0) {
                                        echo '';
                                    }
                                    elseif($avg_rating == 1.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    } 
                                    elseif($avg_rating == 2.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 3.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-o"></i>
                                        ';
                                    }
                                    elseif($avg_rating == 4.5) {
                                        echo '
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                        ';
                                    }
                                    else {
                                        for($i=1;$i<=5;$i++) {
                                            ?>
                                            <?php if($i>$avg_rating): ?>
                                                <i class="fa fa-star-o"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star"></i>
                                            <?php endif; ?>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php if($row['p_qty'] == 0): ?>
                                    <div class="out-of-stock">
                                        <div class="inner">
                                            Stokta Yok
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-shopping-cart"></i> Sepete Ekle</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</div>
<?php endif; ?>




<?php require_once('footer.php'); ?>