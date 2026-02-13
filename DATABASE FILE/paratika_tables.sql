-- =============================================
-- Paratika Payment Gateway Tables for Retail
-- Run this SQL in your MySQL database (ecommerceweb)
-- =============================================

-- 1. Payment Gateway Settings (Paratika API credentials)
CREATE TABLE IF NOT EXISTS `tbl_payment_gateway_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `gateway_name` VARCHAR(50) NOT NULL DEFAULT 'paratika',
  `api_url` VARCHAR(500) NOT NULL DEFAULT '',
  `direct_post_3d_url` VARCHAR(500) NOT NULL DEFAULT '',
  `merchant_id` VARCHAR(100) NOT NULL DEFAULT '',
  `api_username` VARCHAR(100) NOT NULL DEFAULT '',
  `api_password` VARCHAR(255) NOT NULL DEFAULT '',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default empty row
INSERT INTO `tbl_payment_gateway_settings` (`gateway_name`, `api_url`, `direct_post_3d_url`, `merchant_id`, `api_username`, `api_password`, `is_active`)
VALUES ('paratika', '', '', '', '', '', 0);


-- 2. Paratika Payment Results (stores all payment transactions)
CREATE TABLE IF NOT EXISTS `tbl_paratika_payment_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_token` VARCHAR(255) DEFAULT NULL,
  `merchant_payment_id` VARCHAR(100) DEFAULT NULL,
  `pg_order_id` VARCHAR(100) DEFAULT NULL,
  `pg_tran_id` VARCHAR(100) DEFAULT NULL,
  `pg_tran_ref_id` VARCHAR(100) DEFAULT NULL,
  `pg_tran_appr_code` VARCHAR(50) DEFAULT NULL,
  `pg_tran_date` VARCHAR(50) DEFAULT NULL,
  `response_code` VARCHAR(20) DEFAULT 'PENDING',
  `response_msg` TEXT DEFAULT NULL,
  `error_code` VARCHAR(50) DEFAULT NULL,
  `error_msg` TEXT DEFAULT NULL,
  `amount` DECIMAL(12,2) DEFAULT 0.00,
  `currency` VARCHAR(10) DEFAULT 'TRY',
  `installment` VARCHAR(10) DEFAULT '1',
  `card_holder_name` VARCHAR(255) DEFAULT NULL,
  `card_number_first_last_4digit` VARCHAR(20) DEFAULT NULL,
  `payment_system` VARCHAR(100) DEFAULT NULL,
  `bin_card_brand` VARCHAR(50) DEFAULT NULL,
  `bin_card_type` VARCHAR(50) DEFAULT NULL,
  `bin_card_network` VARCHAR(50) DEFAULT NULL,
  `bin_issuer` VARCHAR(100) DEFAULT NULL,
  `customer_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(255) DEFAULT NULL,
  `customer_email` VARCHAR(255) DEFAULT NULL,
  `payment_id` VARCHAR(50) DEFAULT NULL COMMENT 'Links to tbl_payment.payment_id',
  `callback_data` TEXT DEFAULT NULL COMMENT 'JSON callback raw data',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_session_token` (`session_token`),
  INDEX `idx_merchant_payment_id` (`merchant_payment_id`),
  INDEX `idx_payment_id` (`payment_id`),
  INDEX `idx_customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
