<?php

use core\db\DatabaseHandler;

$sql = array();




$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__pagequeue` (
    `pagequeue_id` int(11) NOT NULL AUTO_INCREMENT,
    `ref_id` int(11) DEFAULT NULL,
    `ref_object` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_general_ci,
    `crop_x1` double DEFAULT NULL,
    `crop_y1` double DEFAULT NULL,
    `crop_x2` double DEFAULT NULL,
    `crop_y2` double DEFAULT NULL,
    `degrees_rotated` int(11) DEFAULT NULL,
    `page_orientation` enum('P','L') COLLATE utf8mb4_general_ci DEFAULT 'P',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`pagequeue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__store` (
    `store_id` int(11) NOT NULL AUTO_INCREMENT,
    `store_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `store_name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `note` mediumtext COLLATE utf8mb4_general_ci,
    `last_file_change` bigint(20) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`store_id`),
    UNIQUE KEY `store_name` (`store_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__store_file` (
    `store_file_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `store_id` int(11) DEFAULT NULL,
    `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
    `rev` int(11) DEFAULT NULL,
    `deleted` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`store_file_id`),
    KEY `store_id` (`store_id`),
    CONSTRAINT `filesync__store_file_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `filesync__store` (`store_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__store_file_meta` (
    `store_file_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `store_file_id` bigint(20) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `long_description` text COLLATE utf8mb4_general_ci,
    `document_date` date DEFAULT NULL,
    PRIMARY KEY (`store_file_meta_id`),
    UNIQUE KEY `store_file_id` (`store_file_id`),
    KEY `company_id` (`company_id`),
    CONSTRAINT `filesync__store_file_meta_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `filesync__store_file_meta_ibfk_2` FOREIGN KEY (`store_file_id`) REFERENCES `filesync__store_file` (`store_file_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__store_file_meta_tag` (
    `meta_tag_id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `visible` tinyint(1) DEFAULT '1',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`meta_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `filesync__store_file_rev` (
    `store_file_rev_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `store_file_id` bigint(20) DEFAULT NULL,
    `filesize` bigint(20) DEFAULT NULL,
    `md5sum` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `rev` int(11) DEFAULT NULL,
    `lastmodified` datetime DEFAULT NULL,
    `encrypted` tinyint(1) DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`store_file_rev_id`),
    UNIQUE KEY `uc_store_file_id_rev` (`store_file_id`,`rev`),
    CONSTRAINT `filesync__store_file_rev_ibfk_1` FOREIGN KEY (`store_file_id`) REFERENCES `filesync__store_file` (`store_file_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";



$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}

