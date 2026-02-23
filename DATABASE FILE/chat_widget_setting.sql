-- Add chat widget on/off setting to tbl_settings
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_settings` ADD COLUMN IF NOT EXISTS `chat_widget_on_off` TINYINT(1) NOT NULL DEFAULT 1;
