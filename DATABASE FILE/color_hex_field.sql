-- Add color_code (hex) field to tbl_color
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_color` ADD COLUMN `color_code` VARCHAR(10) DEFAULT '#000000';
