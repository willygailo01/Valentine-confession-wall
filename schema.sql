
CREATE DATABASE IF NOT EXISTS `valentine_confession`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `valentine_confession`;

DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `feedback_comments`;

CREATE TABLE `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` VARCHAR(80) DEFAULT NULL,
  `message` VARCHAR(500) NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_created_at` (`created_at`),
  KEY `idx_messages_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `feedback_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` VARCHAR(80) DEFAULT NULL,
  `feedback_type` VARCHAR(40) NOT NULL DEFAULT 'general',
  `comment` VARCHAR(700) NOT NULL,
  `is_reviewed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_feedback_created_at` (`created_at`),
  KEY `idx_feedback_is_reviewed` (`is_reviewed`),
  KEY `idx_feedback_type` (`feedback_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
