-- Add column count settings to tbl_settings
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `featured_columns` INT NOT NULL DEFAULT 4;
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `latest_columns` INT NOT NULL DEFAULT 4;
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `popular_columns` INT NOT NULL DEFAULT 4;
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `category_banner_columns` INT NOT NULL DEFAULT 4;
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `category_banner_title` VARCHAR(255) NOT NULL DEFAULT 'Kategori Bannerları';
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `category_banner_subtitle` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `product_category_columns` INT NOT NULL DEFAULT 3;
