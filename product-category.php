<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_product_category = $row['banner_product_category'];
    $category_product_columns = isset($row['category_product_columns']) ? intval($row['category_product_columns']) : 3;
}
$col_class = 'col-md-' . intval(12 / $category_product_columns);
?>
<style>
.product-cat-grid {
    display: grid;
    grid-template-columns: repeat(<?php echo $category_product_columns; ?>, 1fr);
    gap: 20px;
}
.product-cat-grid .item-product-cat {
    width: 100%;
    padding: 0;
    float: none;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
}
.product-cat-grid .item-product-cat .inner {
    border: none;
}
.product-cat-grid .item-product-cat .thumb {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: #f9f9f9;
}
.product-cat-grid .item-product-cat .thumb .photo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}
@media (max-width: 991px) {
    .product-cat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
    .product-cat-grid { grid-template-columns: 1fr; }
}
</style>

<?php
if( !isset($_REQUEST['id']) || !isset($_REQUEST['type']) ) {
    header('location: index.php');
    exit;
} else {

    $valid_types = array('top-category','mid-category','end-category','brand','model');
    if( !in_array($_REQUEST['type'], $valid_types) ) {
        header('location: index.php');
        exit;
    } else {

        $statement = $pdo->prepare("SELECT * FROM tbl_top_category");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        foreach ($result as $row) {
            $top[] = $row['tcat_id'];
            $top1[] = $row['tcat_name'];
        }

        $statement = $pdo->prepare("SELECT * FROM tbl_mid_category");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        foreach ($result as $row) {
            $mid[] = $row['mcat_id'];
            $mid1[] = $row['mcat_name'];
            $mid2[] = $row['tcat_id'];
        }

        $statement = $pdo->prepare("SELECT * FROM tbl_end_category");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
        foreach ($result as $row) {
            $end[] = $row['ecat_id'];
            $end1[] = $row['ecat_name'];
            $end2[] = $row['mcat_id'];
        }

        if($_REQUEST['type'] == 'top-category') {
            if(!in_array($_REQUEST['id'],$top)) {
                header('location: index.php');
                exit;
            } else {

                // Getting Title
                for ($i=0; $i < count($top); $i++) { 
                    if($top[$i] == $_REQUEST['id']) {
                        $title = $top1[$i];
                        break;
                    }
                }
                $arr1 = array();
                $arr2 = array();
                // Find out all ecat ids under this
                for ($i=0; $i < count($mid); $i++) { 
                    if($mid2[$i] == $_REQUEST['id']) {
                        $arr1[] = $mid[$i];
                    }
                }
                for ($j=0; $j < count($arr1); $j++) {
                    for ($i=0; $i < count($end); $i++) { 
                        if($end2[$i] == $arr1[$j]) {
                            $arr2[] = $end[$i];
                        }
                    }   
                }
                $final_ecat_ids = $arr2;
            }   
        }

        if($_REQUEST['type'] == 'mid-category') {
            if(!in_array($_REQUEST['id'],$mid)) {
                header('location: index.php');
                exit;
            } else {
                // Getting Title
                for ($i=0; $i < count($mid); $i++) { 
                    if($mid[$i] == $_REQUEST['id']) {
                        $title = $mid1[$i];
                        break;
                    }
                }
                $arr2 = array();        
                // Find out all ecat ids under this
                for ($i=0; $i < count($end); $i++) { 
                    if($end2[$i] == $_REQUEST['id']) {
                        $arr2[] = $end[$i];
                    }
                }
                $final_ecat_ids = $arr2;
            }
        }

        if($_REQUEST['type'] == 'end-category') {
            if(!in_array($_REQUEST['id'],$end)) {
                header('location: index.php');
                exit;
            } else {
                // Getting Title
                for ($i=0; $i < count($end); $i++) { 
                    if($end[$i] == $_REQUEST['id']) {
                        $title = $end1[$i];
                        break;
                    }
                }
                $final_ecat_ids = array($_REQUEST['id']);
            }
        }

        // Brand type - show all products of models under this brand
        if($_REQUEST['type'] == 'brand') {
            $statement_b = $pdo->prepare("SELECT * FROM tbl_brands WHERE brand_id=?");
            $statement_b->execute(array($_REQUEST['id']));
            $result_b = $statement_b->fetchAll(PDO::FETCH_ASSOC);
            if(count($result_b) == 0) {
                header('location: index.php');
                exit;
            } else {
                $title = $result_b[0]['brand_name'];
                // Get all model ids under this brand
                $statement_m = $pdo->prepare("SELECT model_id FROM tbl_models WHERE brand_id=?");
                $statement_m->execute(array($_REQUEST['id']));
                $model_ids = $statement_m->fetchAll(PDO::FETCH_COLUMN);
                // Get all ecat_ids of products with these model_ids
                $final_ecat_ids = array();
                if(count($model_ids) > 0) {
                    $placeholders = implode(',', array_fill(0, count($model_ids), '?'));
                    $statement_p = $pdo->prepare("SELECT DISTINCT ecat_id FROM tbl_product WHERE model_id IN ($placeholders)");
                    $statement_p->execute($model_ids);
                    $final_ecat_ids = $statement_p->fetchAll(PDO::FETCH_COLUMN);
                }
                if(empty($final_ecat_ids)) $final_ecat_ids = array(0);
            }
        }

        // Model type - show all products of this model
        if($_REQUEST['type'] == 'model') {
            $statement_m = $pdo->prepare("SELECT m.*, b.brand_name FROM tbl_models m LEFT JOIN tbl_brands b ON m.brand_id=b.brand_id WHERE m.model_id=?");
            $statement_m->execute(array($_REQUEST['id']));
            $result_m = $statement_m->fetchAll(PDO::FETCH_ASSOC);
            if(count($result_m) == 0) {
                header('location: index.php');
                exit;
            } else {
                $title = $result_m[0]['brand_name'] . ' - ' . $result_m[0]['model_name'];
                // Get all ecat_ids of products with this model_id
                $statement_p = $pdo->prepare("SELECT DISTINCT ecat_id FROM tbl_product WHERE model_id=?");
                $statement_p->execute(array($_REQUEST['id']));
                $final_ecat_ids = $statement_p->fetchAll(PDO::FETCH_COLUMN);
                if(empty($final_ecat_ids)) $final_ecat_ids = array(0);
            }
        }
        
    }   
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_product_category; ?>)">
    <div class="inner">
        <h1><?php echo LANG_VALUE_50; ?> <?php echo $title; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
          <div class="col-md-3">
                <?php require_once('sidebar-category.php'); ?>
            </div>
            <div class="col-md-9">
                
                <h3><?php echo LANG_VALUE_51; ?> "<?php echo $title; ?>"</h3>
                <div class="product product-cat">

                    <div class="product-cat-grid">
                        <?php
                        // Checking if any product is available or not
                        $prod_count = 0;
                        $statement = $pdo->prepare("SELECT * FROM tbl_product");
                        $statement->execute();
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($result as $row) {
                            $prod_table_ecat_ids[] = $row['ecat_id'];
                        }

                        for($ii=0;$ii<count($final_ecat_ids);$ii++):
                            if(in_array($final_ecat_ids[$ii],$prod_table_ecat_ids)) {
                                $prod_count++;
                            }
                        endfor;

                        if($prod_count==0) {
                            echo '<div class="pl_15">'.LANG_VALUE_153.'</div>';
                        } else {
                            for($ii=0;$ii<count($final_ecat_ids);$ii++) {
                                $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE ecat_id=? AND p_is_active=?");
                                $statement->execute(array($final_ecat_ids[$ii],1));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <div class="item item-product-cat">
                                        <div class="inner">
                                            <div class="thumb">
                                                <img class="photo-img" src="assets/uploads/product_photos/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo htmlspecialchars($row['p_name']); ?>">
                                                <div class="overlay"></div>
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
                                                            Out Of Stock
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <p><a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-shopping-cart"></i> <?php echo LANG_VALUE_154; ?></a></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>