<?php
/**
 * Shared Product Card Template
 * Used by index.php (carousels) and product-category.php (grid)
 * 
 * Required variable: $row (product row from tbl_product)
 * Uses LANG_VALUE_1, LANG_VALUE_154 constants
 */
?>
<div class="item item-product-cat">
    <div class="inner">
        <div class="thumb">
            <a href="product.php?id=<?php echo $row['p_id']; ?>" class="thumb-link">
                <img class="photo-img" src="assets/uploads/product_photos/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo htmlspecialchars($row['p_name']); ?>" loading="lazy">
                <div class="overlay"></div>
            </a>
            <button class="quick-view-btn" data-product-id="<?php echo $row['p_id']; ?>" title="Hızlı Bakış">
                <i class="fa fa-search-plus"></i>
            </button>
        </div>
        <div class="text">
            <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
            <h4>
                <?php echo LANG_VALUE_1; ?><?php echo $row['p_current_price']; ?> 
                <?php if($row['p_old_price'] != ''): ?>
                <del>
                    <?php echo LANG_VALUE_1; ?><?php echo $row['p_old_price']; ?>
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

                if($avg_rating == 0) {
                    echo '';
                } elseif($avg_rating == 1.5) {
                    echo '<i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
                } elseif($avg_rating == 2.5) {
                    echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>';
                } elseif($avg_rating == 3.5) {
                    echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i>';
                } elseif($avg_rating == 4.5) {
                    echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i>';
                } else {
                    for($i=1;$i<=5;$i++) {
                        if($i>$avg_rating) {
                            echo '<i class="fa fa-star-o"></i>';
                        } else {
                            echo '<i class="fa fa-star"></i>';
                        }
                    }
                }
                ?>
            </div>
            <?php if($row['p_qty'] == 0): ?>
                <div class="out-of-stock">
                    <div class="inner">
                        <?php echo defined('LANG_VALUE_153') ? LANG_VALUE_153 : 'Stokta Yok'; ?>
                    </div>
                </div>
            <?php else: ?>
                <p><a href="javascript:void(0);" onclick="addToCartQuick(<?php echo $row['p_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['p_name'], ENT_QUOTES)); ?>', '<?php echo $row['p_current_price']; ?>', '<?php echo $row['p_featured_photo']; ?>'); return false;" class="btn-quick-add-cart">
                    <i class="fa fa-shopping-cart"></i> <?php echo defined('LANG_VALUE_154') ? LANG_VALUE_154 : 'Sepete Ekle'; ?></a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
