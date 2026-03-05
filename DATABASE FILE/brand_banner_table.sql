-- Brand Banner Table
-- Run this SQL in your MySQL database (ecommerceweb)

CREATE TABLE IF NOT EXISTS `tbl_brand_banner` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `photo` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `button_text` VARCHAR(255) DEFAULT NULL,
  `button_url` VARCHAR(500) DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings columns for brand banner section
ALTER TABLE `tbl_settings` ADD COLUMN `brand_banner_columns` INT NOT NULL DEFAULT 4;
ALTER TABLE `tbl_settings` ADD COLUMN `brand_banner_title` VARCHAR(255) NOT NULL DEFAULT 'Marka Bannerları';
ALTER TABLE `tbl_settings` ADD COLUMN `brand_banner_subtitle` VARCHAR(255) NOT NULL DEFAULT '';
