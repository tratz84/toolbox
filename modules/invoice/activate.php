<?php

use core\db\DatabaseHandler;

$sql = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `article__article` (
    `article_id` int(11) NOT NULL AUTO_INCREMENT,
    `article_type` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `article_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `long_description1` mediumtext COLLATE utf8mb4_general_ci,
    `long_description2` mediumtext COLLATE utf8mb4_general_ci,
    `price` double DEFAULT NULL,
    `rentable` tinyint(1) DEFAULT '0',
    `simultaneously_rentable` int(11) DEFAULT NULL,
    `price_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `vat_price` bigint(20) DEFAULT NULL,
    `vat_id` int(11) DEFAULT NULL,
    `active` tinyint(1) DEFAULT '1',
    `deleted` tinyint(1) DEFAULT '0',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`article_id`),
    KEY `article__article_ibfk_1` (`vat_id`),
    CONSTRAINT `article__article_ibfk_1` FOREIGN KEY (`vat_id`) REFERENCES `invoice__vat` (`vat_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `article__article_group` (
    `article_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `parent_article_group_id` int(11) DEFAULT NULL,
    `group_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `long_description1` mediumtext COLLATE utf8mb4_general_ci,
    `long_description2` mediumtext COLLATE utf8mb4_general_ci,
    `active` tinyint(1) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`article_group_id`),
    KEY `article__article_group_ibfk_1` (`parent_article_group_id`),
    CONSTRAINT `article__article_group_ibfk_1` FOREIGN KEY (`parent_article_group_id`) REFERENCES `article__article_group` (`article_group_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `article__article_article_group` (
    `article_group_id` int(11) NOT NULL,
    `article_id` int(11) NOT NULL,
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`article_group_id`,`article_id`),
    KEY `article__article_article_group_ibfk_2` (`article_id`),
    CONSTRAINT `article__article_article_group_ibfk_1` FOREIGN KEY (`article_group_id`) REFERENCES `article__article_group` (`article_group_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT `article__article_article_group_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article__article` (`article_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";



$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__company_setting` (
    `company_setting_id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) DEFAULT NULL,
    `tax_shift` tinyint(1) DEFAULT '0',
    `tax_excemption` tinyint(1) DEFAULT '0',
    `payment_term` int(11) DEFAULT NULL,
    PRIMARY KEY (`company_setting_id`),
    UNIQUE KEY `company_id` (`company_id`),
    CONSTRAINT `invoice__company_setting_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__invoice_status` (
    `invoice_status_id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `default_selected` tinyint(1) DEFAULT '0',
    `active` tinyint(1) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`invoice_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__invoice` (
    `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
    `ref_invoice_id` int(11) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `invoice_status_id` int(11) DEFAULT NULL,
    `credit_invoice` tinyint(1) DEFAULT '0',
    `tax_shift` tinyint(1) DEFAULT '0',
    `invoice_number` int(11) DEFAULT NULL,
    `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `comment` mediumtext COLLATE utf8mb4_general_ci,
    `note` text COLLATE utf8mb4_general_ci,
    `total_calculated_price` decimal(10,2) DEFAULT NULL,
    `total_calculated_price_incl_vat` decimal(10,2) DEFAULT NULL,
    `invoice_date` date DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`invoice_id`),
    UNIQUE KEY `invoice_number` (`invoice_number`),
    KEY `invoice__invoice_ibfk_1` (`invoice_status_id`),
    CONSTRAINT `invoice__invoice_ibfk_1` FOREIGN KEY (`invoice_status_id`) REFERENCES `invoice__invoice_status` (`invoice_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__invoice_line` (
    `invoice_line_id` int(11) NOT NULL AUTO_INCREMENT,
    `invoice_id` int(11) DEFAULT NULL,
    `article_id` int(11) DEFAULT NULL,
    `short_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `amount` double DEFAULT NULL,
    `price` decimal(10,2) DEFAULT NULL,
    `vat_percentage` double DEFAULT NULL,
    `vat_amount` decimal(10,2) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`invoice_line_id`),
    KEY `invoice__invoice_line_ibfk_1` (`invoice_id`),
    CONSTRAINT `invoice__invoice_line_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice__invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__offer_status` (
    `offer_status_id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `default_selected` tinyint(1) DEFAULT NULL,
    `active` tinyint(1) DEFAULT '1',
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`offer_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__offer` (
    `offer_id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `offer_number` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `offer_status_id` int(11) DEFAULT NULL,
    `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `comment` mediumtext COLLATE utf8mb4_general_ci,
    `note` text COLLATE utf8mb4_general_ci,
    `total_calculated_price` decimal(10,2) DEFAULT NULL,
    `total_calculated_price_incl_vat` decimal(10,2) DEFAULT NULL,
    `accepted` tinyint(1) DEFAULT '0',
    `offer_date` date DEFAULT NULL,
    `deposit` double DEFAULT NULL,
    `payment_upfront` double DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`offer_id`),
    KEY `offer_status_id` (`offer_status_id`),
    CONSTRAINT `invoice__offer_ibfk_1` FOREIGN KEY (`offer_status_id`) REFERENCES `invoice__offer_status` (`offer_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__offer_file` (
    `offer_file_id` int(11) NOT NULL AUTO_INCREMENT,
    `offer_id` int(11) DEFAULT NULL,
    `file_id` int(11) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`offer_file_id`),
    UNIQUE KEY `uq_offer_id_file_id` (`offer_id`,`file_id`),
    KEY `file_id` (`file_id`),
    CONSTRAINT `invoice__offer_file_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `base__file` (`file_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__offer_line` (
    `offer_line_id` int(11) NOT NULL AUTO_INCREMENT,
    `offer_id` int(11) DEFAULT NULL,
    `article_id` int(11) DEFAULT NULL,
    `short_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `short_description2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `long_description` mediumtext COLLATE utf8mb4_general_ci,
    `amount` double DEFAULT NULL,
    `price` double DEFAULT NULL,
    `vat` double DEFAULT NULL,
    `line_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT 'price',
    `sort` int(11) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`offer_line_id`),
    KEY `offer_id` (`offer_id`),
    CONSTRAINT `invoice__offer_line_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `invoice__offer` (`offer_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";


$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__price_adjustment` (
    `price_adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `ref_object` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `ref_id` int(11) DEFAULT NULL,
    `new_price` decimal(10,2) DEFAULT NULL,
    `new_discount` decimal(10,2) DEFAULT NULL,
    `start_date` date DEFAULT NULL,
    `executed` tinyint(1) DEFAULT '0',
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`price_adjustment_id`),
    KEY `ref_object` (`ref_object`,`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";


$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__to_bill` (
    `to_bill_id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `project_id` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `short_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `amount` double DEFAULT NULL,
    `price` decimal(10,2) DEFAULT NULL,
    `invoice_line_id` int(11) DEFAULT NULL,
    `billed` tinyint(1) DEFAULT '0',
    `deleted` datetime DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`to_bill_id`),
    KEY `company_id` (`company_id`),
    KEY `project_id` (`project_id`),
    KEY `user_id` (`user_id`),
    KEY `invoice_line_id` (`invoice_line_id`),
    CONSTRAINT `invoice__to_bill_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE SET NULL ON UPDATE CASCADE,
--    CONSTRAINT `invoice__to_bill_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project__project` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `invoice__to_bill_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `invoice__to_bill_ibfk_4` FOREIGN KEY (`invoice_line_id`) REFERENCES `invoice__invoice_line` (`invoice_line_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `invoice__vat` (
    `vat_id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `percentage` double DEFAULT NULL,
    `visible` tinyint(1) DEFAULT '1',
    `default_selected` tinyint(1) DEFAULT '0',
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`vat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";


$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}
