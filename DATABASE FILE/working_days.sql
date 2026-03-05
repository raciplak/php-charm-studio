-- Add working days field to tbl_settings
-- Run this SQL in your MySQL database (ecommerceweb)
-- If column already exists, this will give a harmless error

ALTER TABLE `tbl_settings` ADD COLUMN `working_days` VARCHAR(500) NOT NULL DEFAULT '';
