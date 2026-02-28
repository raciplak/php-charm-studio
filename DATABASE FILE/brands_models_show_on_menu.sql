-- Add show_on_menu field to tbl_brands and tbl_models
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_brands` ADD COLUMN `show_on_menu` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `tbl_models` ADD COLUMN `show_on_menu` TINYINT(1) NOT NULL DEFAULT 0;
