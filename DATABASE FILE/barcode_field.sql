-- Add barcode field to tbl_product
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_product` ADD COLUMN `p_barcode` VARCHAR(100) DEFAULT NULL AFTER `p_name`;
ALTER TABLE `tbl_product` ADD INDEX `idx_barcode` (`p_barcode`);
