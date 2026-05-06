-- Migration: table subscriptions
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tiers_id`    INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED NULL,
  `label`       VARCHAR(255) NOT NULL DEFAULT '',
  `amount`      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `recurrence`  ENUM('monthly','quarterly','annual','one_time') NOT NULL DEFAULT 'monthly',
  `start_date`  DATE NULL,
  `end_date`    DATE NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_tiers`   (`tiers_id`),
  KEY `idx_sub_product` (`product_id`),
  KEY `idx_sub_active`  (`is_active`),
  KEY `idx_sub_rec`     (`recurrence`),
  CONSTRAINT `fk_sub_tiers`   FOREIGN KEY (`tiers_id`)   REFERENCES `tiers`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sub_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
