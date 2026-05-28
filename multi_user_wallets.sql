CREATE TABLE IF NOT EXISTS `user_accounts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `bank_plan_id` BIGINT UNSIGNED NULL,
  `account_number` VARCHAR(255) NOT NULL,
  `label` VARCHAR(255) NULL,
  `balance` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `plan_end_date` TIMESTAMP NULL DEFAULT NULL,
  `approved_by` BIGINT UNSIGNED NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `rejected_by` BIGINT UNSIGNED NULL,
  `rejected_at` TIMESTAMP NULL DEFAULT NULL,
  `disabled_by` BIGINT UNSIGNED NULL,
  `disabled_at` TIMESTAMP NULL DEFAULT NULL,
  `admin_note` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_accounts_account_number_unique` (`account_number`),
  KEY `user_accounts_user_id_index` (`user_id`),
  KEY `user_accounts_bank_plan_id_index` (`bank_plan_id`),
  KEY `user_accounts_is_default_index` (`is_default`),
  KEY `user_accounts_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `transactions` ADD COLUMN IF NOT EXISTS `account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `transactions_account_id_index` (`account_id`);
ALTER TABLE `deposits` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `deposits_account_id_index` (`account_id`);
ALTER TABLE `withdraws` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `withdraws_account_id_index` (`account_id`);
ALTER TABLE `wire_transfers` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `wire_transfers_account_id_index` (`account_id`);
ALTER TABLE `user_loans` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `user_loans_account_id_index` (`account_id`);
ALTER TABLE `user_dps` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `user_dps_account_id_index` (`account_id`);
ALTER TABLE `user_fdrs` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `user_fdrs_account_id_index` (`account_id`);
ALTER TABLE `money_requests` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `money_requests_account_id_index` (`account_id`);
ALTER TABLE `balance_transfers` ADD COLUMN IF NOT EXISTS`account_id` BIGINT UNSIGNED NULL AFTER `user_id`, ADD INDEX `balance_transfers_account_id_index` (`account_id`);
ALTER TABLE `balance_transfers` ADD COLUMN IF NOT EXISTS`receiver_account_id` BIGINT UNSIGNED NULL AFTER `receiver_id`, ADD INDEX `balance_transfers_receiver_account_id_index` (`receiver_account_id`);
ALTER TABLE `money_requests` ADD COLUMN IF NOT EXISTS`receiver_account_id` BIGINT UNSIGNED NULL AFTER `receiver_id`, ADD INDEX `money_requests_receiver_account_id_index` (`receiver_account_id`);

INSERT INTO `user_accounts` (
  `user_id`,
  `bank_plan_id`,
  `account_number`,
  `label`,
  `balance`,
  `is_default`,
  `status`,
  `plan_end_date`,
  `approved_at`,
  `created_at`,
  `updated_at`
)
SELECT
  u.`id`,
  u.`bank_plan_id`,
  COALESCE(
    NULLIF(u.`account_number`, ''),
    CONCAT(
      COALESCE((SELECT `account_no_prefix` FROM `generalsettings` LIMIT 1), 'AC'),
      DATE_FORMAT(NOW(), '%y%d%i%s'),
      LPAD(u.`id`, 6, '0')
    )
  ),
  'Default Account',
  COALESCE(u.`balance`, 0),
  1,
  'active',
  u.`plan_end_date`,
  NOW(),
  NOW(),
  NOW()
FROM `users` u
WHERE NOT EXISTS (
  SELECT 1
  FROM `user_accounts` ua
  WHERE ua.`user_id` = u.`id`
  AND ua.`is_default` = 1
);

UPDATE `transactions`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `transactions`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `deposits`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `deposits`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `withdraws`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `withdraws`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `wire_transfers`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `wire_transfers`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `user_loans`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `user_loans`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `user_dps`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `user_dps`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `user_fdrs`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `user_fdrs`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `balance_transfers`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `balance_transfers`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `balance_transfers`
SET `receiver_account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `balance_transfers`.`receiver_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `receiver_account_id` IS NULL
AND `receiver_id` IS NOT NULL;

UPDATE `money_requests`
SET `account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `money_requests`.`user_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `account_id` IS NULL;

UPDATE `money_requests`
SET `receiver_account_id` = (
  SELECT `id` FROM `user_accounts`
  WHERE `user_accounts`.`user_id` = `money_requests`.`receiver_id`
  AND `is_default` = 1
  LIMIT 1
)
WHERE `receiver_account_id` IS NULL
AND `receiver_id` IS NOT NULL;