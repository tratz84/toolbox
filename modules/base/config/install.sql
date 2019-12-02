
CREATE TABLE IF NOT EXISTS `base__activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `ref_object` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_general_ci,
  `long_description` text COLLATE utf8mb4_general_ci,
  `note` text COLLATE utf8mb4_general_ci,
  `changes` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__cron` (
  `cron_id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_status` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `running` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__cron_run` (
  `cron_run_id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `error` text COLLATE utf8mb4_general_ci,
  `status` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`cron_run_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_count` int(11) DEFAULT '0',
  `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `module_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_menu_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`menu_id`),
  UNIQUE KEY `menu_code` (`menu_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__multiuser_lock` (
  `username` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `tabuid` varchar(48) COLLATE utf8mb4_general_ci NOT NULL,
  `lock_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`username`,`tabuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__object_meta` (
  `object_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `object_key` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_value` longtext COLLATE utf8mb4_general_ci,
  `object_note` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`object_meta_id`),
  UNIQUE KEY `index_key_id` (`object_name`,`object_key`,`object_id`)
) ENGINE=InnoDB  CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__reset_password` (
  `reset_password_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `security_string` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `request_ip` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `used_ip` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `used` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`reset_password_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `setting_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_description` longtext COLLATE utf8mb4_general_ci,
  `long_description` longtext COLLATE utf8mb4_general_ci,
  `text_value` longtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_code` (`setting_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `base__user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `user_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `firstname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `autologin_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__user_capability` (
  `user_capability_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module_name` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `capability_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_capability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `base__user_ip` (
  `user_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`user_ip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



