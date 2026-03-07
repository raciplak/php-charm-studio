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
                <img class="photo-img" src="assets/uploads/product_photos/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo htmlspecialchars($row['p_name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                <div class="overlay"></div>
            </a>
            <button class="quick-view-btn" data-product-id="<?php echo $row['p_id']; ?>" title="Hızlı Bakış">
                <i class="fa fa-search-plus"></i>
            </button>
        </div>
        <div class="text">
            <h3><a href="product.php?id=<?php echo $row['p_id']; ?>" title="<?php echo htmlspecialchars($row['p_name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $row['p_name']; ?></a></h3>
            <div class="card-bottom">
                <h4>
                    <?php echo LANG_VALUE_1; ?><?php echo $row['p_current_price']; ?>
                    <?php if($row['p_old_price'] != ''): ?>
                    <del><?php echo LANG_VALUE_1; ?><?php echo $row['p_old_price']; ?></del>
                    <?php endif; ?>
                </h4>
                <?php if($row['p_qty'] == 0): ?>
                    <div class="out-of-stock">
                        <div class="inner">
                            <?php echo defined('LANG_VALUE_153') ? LANG_VALUE_153 : 'Tükendi'; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p><a href="javascript:void(0);" class="btn-quick-add-cart" 
                        data-id="<?php echo $row['p_id']; ?>" 
                        data-name="<?php echo htmlspecialchars($row['p_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                        data-price="<?php echo $row['p_current_price']; ?>" 
                        data-photo="<?php echo $row['p_featured_photo']; ?>">
                        <i class="fa fa-shopping-cart"></i> Sepete Ekle</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
