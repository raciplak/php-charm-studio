-- Fix tbl_page charset for Turkish characters
-- Run this SQL in your MySQL database (ecommerceweb)

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `tbl_page` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
