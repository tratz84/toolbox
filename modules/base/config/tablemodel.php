<?php

use core\db\TableModel;

$tbs = array();


$tb_activity = new TableModel('base', 'activity');
$tb_activity->addColumn('activity_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_activity->addColumn('user_id', 'int');
$tb_activity->addColumn('username', 'varchar(128)');
$tb_activity->addColumn('company_id', 'int');
$tb_activity->addColumn('person_id', 'int');
$tb_activity->addColumn('ref_object', 'varchar(32)');
$tb_activity->addColumn('ref_id', 'int');
$tb_activity->addColumn('code', 'varchar(32)');
$tb_activity->addColumn('short_description', 'text');
$tb_activity->addColumn('long_description', 'text');
$tb_activity->addColumn('note', 'text');
$tb_activity->addColumn('changes', 'text');
$tb_activity->addColumn('created', 'datetime');
$tbs[] = $tb_activity;

$tb_cron = new TableModel('base', 'cron');
$tb_cron->addColumn('cron_id', 'int', ['key' => 'PRIMARY KEY', 'auto_increment' => true]);
$tb_cron->addColumn('cron_name', 'varchar(128)');
$tb_cron->addColumn('last_run', 'datetime');
$tb_cron->addColumn('running', 'boolean');
$tbs[] = $tb_cron;



// CREATE TABLE IF NOT EXISTS `base__cron_run` (
//   `cron_run_id` int(11) NOT NULL AUTO_INCREMENT,
//   `cron_id` int(11) DEFAULT NULL,
//   `message` text COLLATE utf8mb4_general_ci,
//   `error` text COLLATE utf8mb4_general_ci,
//   `status` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`cron_run_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__file` (
//   `file_id` int(11) NOT NULL AUTO_INCREMENT,
//   `ref_count` int(11) DEFAULT '0',
//   `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `filesize` int(11) DEFAULT NULL,
//   `module_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `category_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`file_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__menu` (
//   `menu_id` int(11) NOT NULL AUTO_INCREMENT,
//   `menu_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `parent_menu_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   `visible` tinyint(1) DEFAULT '1',
//   PRIMARY KEY (`menu_id`),
//   UNIQUE KEY `menu_code` (`menu_code`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__multiuser_lock` (
//   `username` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
//   `tabuid` varchar(48) COLLATE utf8mb4_general_ci NOT NULL,
//   `lock_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `ip` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`username`,`tabuid`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__object_meta` (
//   `object_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
//   `object_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `object_key` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `object_id` int(11) DEFAULT NULL,
//   `object_value` longtext COLLATE utf8mb4_general_ci,
//   `object_note` text COLLATE utf8mb4_general_ci,
//   PRIMARY KEY (`object_meta_id`),
//   UNIQUE KEY `index_key_id` (`object_name`,`object_key`,`object_id`)
// ) ENGINE=InnoDB  CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__reset_password` (
//   `reset_password_id` int(11) NOT NULL AUTO_INCREMENT,
//   `user_id` int(11) DEFAULT NULL,
//   `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `security_string` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `request_ip` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `used_ip` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `used` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`reset_password_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__setting` (
//   `setting_id` int(11) NOT NULL AUTO_INCREMENT,
//   `setting_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `setting_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `short_description` longtext COLLATE utf8mb4_general_ci,
//   `long_description` longtext COLLATE utf8mb4_general_ci,
//   `text_value` longtext COLLATE utf8mb4_general_ci,
//   PRIMARY KEY (`setting_id`),
//   UNIQUE KEY `setting_code` (`setting_code`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


// CREATE TABLE IF NOT EXISTS `base__user` (
//   `user_id` int(11) NOT NULL AUTO_INCREMENT,
//   `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   `user_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `firstname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `lastname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `autologin_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `activated` tinyint(1) DEFAULT '1',
//   PRIMARY KEY (`user_id`),
//   UNIQUE KEY `username` (`username`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__user_capability` (
//   `user_capability_id` int(11) NOT NULL AUTO_INCREMENT,
//   `user_id` int(11) DEFAULT NULL,
//   `module_name` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
//   `capability_code` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`user_capability_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `base__user_ip` (
//   `user_ip_id` int(11) NOT NULL AUTO_INCREMENT,
//   `user_id` int(11) DEFAULT NULL,
//   `ip` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   PRIMARY KEY (`user_ip_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




// CREATE TABLE `customer__country` (
//   `country_id` int(11) NOT NULL AUTO_INCREMENT,
//   `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `country_iso2` varchar(2) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `country_iso3` varchar(3) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `country_no` varchar(3) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `phone_prefix` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   PRIMARY KEY (`country_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__address` (
//   `address_id` int(11) NOT NULL AUTO_INCREMENT,
//   `street` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `street_no` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `zipcode` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `city` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `note` longtext COLLATE utf8mb4_general_ci,
//   `sort` int(11) DEFAULT NULL,
//   `country_id` int(11) DEFAULT NULL,
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`address_id`),
//   KEY `country_id` (`country_id`),
//   CONSTRAINT `customer__address_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `customer__country` (`country_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__company_type` (
//   `company_type_id` int(11) NOT NULL AUTO_INCREMENT,
//   `type_name` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `default_selected` tinyint(1) DEFAULT '0',
//   `sort` int(11) DEFAULT '0',
//   PRIMARY KEY (`company_type_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__company` (
//   `company_id` int(11) NOT NULL AUTO_INCREMENT,
//   `company_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `contact_person` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `coc_number` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `vat_number` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `iban` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `bic` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `note` longtext COLLATE utf8mb4_general_ci,
//   `deleted` tinyint(1) DEFAULT '0',
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   `company_type_id` int(11) DEFAULT NULL,
//   PRIMARY KEY (`company_id`),
//   KEY `company_type_id` (`company_type_id`),
//   CONSTRAINT `customer__company_ibfk_1` FOREIGN KEY (`company_type_id`) REFERENCES `customer__company_type` (`company_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__company_address` (
//   `company_address_id` int(11) NOT NULL AUTO_INCREMENT,
//   `company_id` int(11) DEFAULT NULL,
//   `address_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`company_address_id`),
//   KEY `company_id` (`company_id`),
//   KEY `address_id` (`address_id`),
//   CONSTRAINT `customer__company_address_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__company_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__email` (
//   `email_id` int(11) NOT NULL AUTO_INCREMENT,
//   `email_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `description` longtext COLLATE utf8mb4_general_ci,
//   `primary_address` tinyint(1) DEFAULT '0',
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`email_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__company_email` (
//   `company_email_id` int(11) NOT NULL AUTO_INCREMENT,
//   `email_id` int(11) DEFAULT NULL,
//   `company_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`company_email_id`),
//   KEY `company_id` (`company_id`),
//   KEY `email_id` (`email_id`),
//   CONSTRAINT `customer__company_email_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__company_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__phone` (
//   `phone_id` int(11) NOT NULL AUTO_INCREMENT,
//   `phonenr` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   `note` longtext COLLATE utf8mb4_general_ci,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`phone_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__company_phone` (
//   `company_phone_id` int(11) NOT NULL AUTO_INCREMENT,
//   `phone_id` int(11) DEFAULT NULL,
//   `company_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`company_phone_id`),
//   KEY `company_id` (`company_id`),
//   KEY `phone_id` (`phone_id`),
//   CONSTRAINT `customer__company_phone_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__company_phone_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


// CREATE TABLE IF NOT EXISTS `customer__person` (
//   `person_id` int(11) NOT NULL AUTO_INCREMENT,
//   `firstname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `insert_lastname` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `lastname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `iban` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `bic` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
//   `note` longtext COLLATE utf8mb4_general_ci,
//   `deleted` tinyint(1) DEFAULT '0',
//   `edited` datetime DEFAULT NULL,
//   `created` datetime DEFAULT NULL,
//   PRIMARY KEY (`person_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__person_address` (
//   `person_address_id` int(11) NOT NULL AUTO_INCREMENT,
//   `person_id` int(11) DEFAULT NULL,
//   `address_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`person_address_id`),
//   KEY `person_id` (`person_id`),
//   KEY `address_id` (`address_id`),
//   CONSTRAINT `customer__person_address_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__person_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__person_email` (
//   `person_email_id` int(11) NOT NULL AUTO_INCREMENT,
//   `email_id` int(11) DEFAULT NULL,
//   `person_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`person_email_id`),
//   KEY `person_id` (`person_id`),
//   KEY `email_id` (`email_id`),
//   CONSTRAINT `customer__person_email_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__person_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

// CREATE TABLE IF NOT EXISTS `customer__person_phone` (
//   `person_phone_id` int(11) NOT NULL AUTO_INCREMENT,
//   `person_id` int(11) DEFAULT NULL,
//   `phone_id` int(11) DEFAULT NULL,
//   `sort` int(11) DEFAULT NULL,
//   PRIMARY KEY (`person_phone_id`),
//   KEY `person_id` (`person_id`),
//   KEY `phone_id` (`phone_id`),
//   CONSTRAINT `customer__person_phone_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
//   CONSTRAINT `customer__person_phone_ibfk_2` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE ON UPDATE RESTRICT
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


