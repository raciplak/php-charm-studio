-- Add sticky on/off settings for top bar, search bar, and navigation bar
-- Run this SQL in your MySQL database (ecommerceweb)

ALTER TABLE `tbl_settings` ADD COLUMN `sticky_topbar_on_off` TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE `tbl_settings` ADD COLUMN `sticky_navbar_on_off` TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE `tbl_settings` ADD COLUMN `sticky_searchbar_on_off` TINYINT(1) NOT NULL DEFAULT 1;
