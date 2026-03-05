-- Add working days field to tbl_settings
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `working_days` VARCHAR(500) NOT NULL DEFAULT 'Pazartesi - Cumartesi: 09:00 - 18:00\nPazar: Kapalı';
