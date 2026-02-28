<?php
require_once('inc/config.php');
require_once('inc/functions.php');

if(isset($_POST['id'])) {
    $brand_id = $_POST['id'];
    $statement = $pdo->prepare("SELECT * FROM tbl_models WHERE brand_id=? ORDER BY model_name ASC");
    $statement->execute(array($brand_id));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<option value="">Select Model</option>';
    foreach($result as $row) {
        echo '<option value="'.$row['model_id'].'">'.$row['model_name'].' ('.$row['model_code'].')</option>';
    }
}
?>
