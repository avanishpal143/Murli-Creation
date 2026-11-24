-- =============================================================================
-- Murli Creations - Enterprise E-Commerce & Studio Admin Schema
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB, and Hostinger Shared Hosting
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- 1. mc_admins (Login & Team User Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `role` ENUM('super_admin', 'manager', 'support') NOT NULL DEFAULT 'super_admin',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `avatar` VARCHAR(255) DEFAULT '/images/brand/Favicon.png',
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2. mc_admin_logs (Security & Audit Logs)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_admin_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT NULL,
  `username` VARCHAR(60) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_admin_action` (`action`),
  INDEX `idx_admin_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. mc_products (Product Management & Product Details)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_products` (
  `id` VARCHAR(64) PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `subcategory` VARCHAR(100) NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `mrp` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `size` VARCHAR(120) NULL,
  `material` VARCHAR(255) NULL,
  `origin` VARCHAR(100) DEFAULT 'Delhi, India',
  `artist` VARCHAR(100) DEFAULT 'Master Craftsman Murli Prasad',
  `rating` DECIMAL(3,2) DEFAULT 4.90,
  `reviews_count` INT DEFAULT 15,
  `images` LONGTEXT NOT NULL COMMENT 'JSON array of image paths',
  `video` VARCHAR(255) NULL,
  `folklore` TEXT NULL,
  `description` LONGTEXT NULL,
  `featured` TINYINT(1) DEFAULT 0,
  `bestseller` TINYINT(1) DEFAULT 0,
  `tags` LONGTEXT NULL COMMENT 'JSON array of string tags',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`),
  INDEX `idx_stock` (`stock`),
  INDEX `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. mc_customers (User Details & Customer Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `address` TEXT NULL,
  `city` VARCHAR(100) NULL,
  `state` VARCHAR(100) NULL,
  `pincode` VARCHAR(20) NULL,
  `total_orders` INT DEFAULT 0,
  `total_spent` DECIMAL(12,2) DEFAULT 0.00,
  `tier` VARCHAR(30) DEFAULT 'Patron',
  `status` ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. mc_orders (Product Order Details & Tracking)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_orders` (
  `id` VARCHAR(64) PRIMARY KEY,
  `customer_id` INT NULL,
  `customer_name` VARCHAR(120) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `customer_address` TEXT NOT NULL,
  `customer_city` VARCHAR(100) NOT NULL,
  `customer_state` VARCHAR(100) NOT NULL,
  `customer_pincode` VARCHAR(20) NOT NULL,
  `items` LONGTEXT NOT NULL COMMENT 'JSON array of items',
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` VARCHAR(50) NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `token_paid` DECIMAL(10,2) DEFAULT 0.00,
  `balance_on_delivery` DECIMAL(10,2) DEFAULT 0.00,
  `payment_method` VARCHAR(50) DEFAULT 'Partial COD',
  `payment_status` ENUM('Pending', 'Paid', 'Failed', 'Refunded') DEFAULT 'Pending',
  `order_status` ENUM('Pending', 'Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled') DEFAULT 'Pending',
  `tracking_carrier` VARCHAR(100) NULL,
  `tracking_number` VARCHAR(100) NULL,
  `tracking_url` VARCHAR(255) NULL,
  `estimated_delivery` DATE NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_customer_email` (`customer_email`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 6. mc_order_status_logs (Status Change History Audit)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_order_status_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` VARCHAR(64) NOT NULL,
  `old_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `comment` TEXT NULL,
  `changed_by` VARCHAR(60) DEFAULT 'System',
  `notified_customer` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 7. mc_crm_leads (CRM & Inquiries Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_crm_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `subject` VARCHAR(200) DEFAULT 'General Inquiry',
  `message` TEXT NOT NULL,
  `lead_status` ENUM('New', 'Contacted', 'In Discussion', 'Quote Sent', 'Converted', 'Closed') DEFAULT 'New',
  `priority` ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
  `follow_up_date` DATE NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_lead_status` (`lead_status`),
  INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 8. mc_crm_interactions (CRM Customer Activity & Notes)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_crm_interactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NULL,
  `customer_id` INT NULL,
  `channel` ENUM('Phone Call', 'WhatsApp', 'Email', 'Studio Visit', 'Internal Note') DEFAULT 'Internal Note',
  `summary` TEXT NOT NULL,
  `next_step` VARCHAR(255) NULL,
  `created_by` VARCHAR(60) DEFAULT 'Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 9. mc_seo_pages (Per-Page SEO Tag Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_seo_pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_key` VARCHAR(60) NOT NULL UNIQUE,
  `page_name` VARCHAR(100) NOT NULL,
  `url_path` VARCHAR(200) NOT NULL,
  `meta_title` VARCHAR(255) NOT NULL,
  `meta_description` TEXT NOT NULL,
  `meta_keywords` VARCHAR(255) NULL,
  `canonical_url` VARCHAR(255) NULL,
  `og_title` VARCHAR(255) NULL,
  `og_description` TEXT NULL,
  `og_image` VARCHAR(255) NULL,
  `twitter_card` VARCHAR(50) DEFAULT 'summary_large_image',
  `robots` VARCHAR(50) DEFAULT 'index, follow',
  `schema_json` LONGTEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 10. mc_coupons (Offer Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
  `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 10.00,
  `min_order` DECIMAL(10,2) DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) DEFAULT 0.00,
  `expiry_date` DATE NULL,
  `usage_limit` INT DEFAULT 0,
  `usage_count` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 11. mc_offers_cta (Offers & Call-to-Action Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_offers_cta` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `section_key` VARCHAR(60) NOT NULL UNIQUE,
  `title` VARCHAR(255) NULL,
  `badge` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `cta_text` VARCHAR(100) NULL,
  `cta_link` VARCHAR(255) NULL,
  `coupon_code` VARCHAR(50) NULL,
  `discount_label` VARCHAR(100) NULL,
  `image_url` VARCHAR(255) NULL,
  `extra_data` LONGTEXT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 12. mc_reminders (Customer Reminder Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_reminders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(120) NOT NULL,
  `customer_phone` VARCHAR(30) NULL,
  `customer_email` VARCHAR(150) NULL,
  `order_id` VARCHAR(64) NULL,
  `reminder_type` ENUM('abandoned_cart', 'pending_payment', 'festive_offer', 'review_request', 'custom') DEFAULT 'abandoned_cart',
  `channel` ENUM('whatsapp', 'email', 'both') DEFAULT 'whatsapp',
  `message_body` TEXT NOT NULL,
  `status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
  `sent_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 13. mc_email_templates (Email Communication Templates)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_email_templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_key` VARCHAR(60) NOT NULL UNIQUE,
  `template_name` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body_html` LONGTEXT NOT NULL,
  `available_vars` VARCHAR(255) NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 14. mc_email_logs (Email Communication History)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_email_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `recipient` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `template_key` VARCHAR(60) NULL,
  `status` ENUM('sent', 'failed') DEFAULT 'sent',
  `error_message` TEXT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 15. mc_api_keys (External Shipping & Webhook API Management)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_api_keys` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL,
  `api_key` VARCHAR(64) NOT NULL UNIQUE,
  `service` VARCHAR(60) DEFAULT 'Generic Webhook',
  `permissions` VARCHAR(100) DEFAULT 'read_write',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_used_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 16. mc_api_webhook_logs (External Status Change Logs)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_api_webhook_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `endpoint` VARCHAR(100) NOT NULL,
  `method` VARCHAR(10) NOT NULL,
  `caller_ip` VARCHAR(45) NULL,
  `payload` LONGTEXT NULL,
  `response_code` INT DEFAULT 200,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 17. mc_site_settings (Site-Wide Key-Value Settings)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_site_settings` (
  `setting_key` VARCHAR(80) PRIMARY KEY,
  `setting_value` LONGTEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 18. mc_reviews (Customer Reviews)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_reviews` (
  `id` VARCHAR(64) PRIMARY KEY,
  `product_id` VARCHAR(64) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(100) DEFAULT 'India',
  `rating` DECIMAL(2,1) DEFAULT 5.0,
  `comment` TEXT NOT NULL,
  `date_text` VARCHAR(50) NULL,
  `is_approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 19. mc_contact_inquiries (Contact Form Submissions & Replies)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mc_contact_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NULL,
  `phone` VARCHAR(40) NOT NULL,
  `subject` VARCHAR(255) DEFAULT 'General Inquiry',
  `message` LONGTEXT NOT NULL,
  `status` ENUM('new', 'read', 'replied', 'resolved', 'archived') DEFAULT 'new',
  `reply_channel` VARCHAR(50) NULL,
  `reply_message` LONGTEXT NULL,
  `replied_at` DATETIME NULL,
  `replied_by` VARCHAR(100) NULL,
  `admin_notes` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_inq_status` (`status`),
  INDEX `idx_inq_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

