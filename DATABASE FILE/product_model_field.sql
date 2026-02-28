-- Add model_id field to tbl_product
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_product` ADD COLUMN `model_id` INT DEFAULT NULL AFTER `ecat_id`;
ALTER TABLE `tbl_product` ADD INDEX `idx_model_id` (`model_id`);
