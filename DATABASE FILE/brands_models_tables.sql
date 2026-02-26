-- Brand & Model Tables
-- Run this SQL in your MySQL database (ecommerceweb)

CREATE TABLE IF NOT EXISTS `tbl_brands` (
  `brand_id` INT AUTO_INCREMENT PRIMARY KEY,
  `brand_code` VARCHAR(50) NOT NULL,
  `brand_name` VARCHAR(255) NOT NULL,
  INDEX `idx_brand_code` (`brand_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_models` (
  `model_id` INT AUTO_INCREMENT PRIMARY KEY,
  `model_code` VARCHAR(50) NOT NULL,
  `model_name` VARCHAR(255) NOT NULL,
  `brand_id` INT NOT NULL,
  INDEX `idx_model_code` (`model_code`),
  INDEX `idx_brand_id` (`brand_id`),
  FOREIGN KEY (`brand_id`) REFERENCES `tbl_brands`(`brand_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
