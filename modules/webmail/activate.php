<?php

use core\db\DatabaseHandler;

$sql = array();





$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__connector` (
    `connector_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `connector_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `hostname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `port` int(11) DEFAULT NULL,
    `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `nextrun_fullimport` tinyint(1) DEFAULT NULL,
    `sent_connector_imapfolder_id` int(11) DEFAULT NULL,
    `junk_connector_imapfolder_id` int(11) DEFAULT NULL,
    `trash_connector_imapfolder_id` int(11) DEFAULT NULL,
    `active` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`connector_id`),
    KEY `email__connector_ibfk_1` (`user_id`),
    CONSTRAINT `email__connector_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__connector_imapfolder` (
    `connector_imapfolder_id` int(11) NOT NULL AUTO_INCREMENT,
    `connector_id` int(11) DEFAULT NULL,
    `folderName` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `attributes` int(11) DEFAULT NULL,
    `outgoing` tinyint(1) DEFAULT NULL,
    `junk` tinyint(1) DEFAULT NULL,
    `active` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`connector_imapfolder_id`),
    KEY `webmail__connector_imapfolder_ibfk_1` (`connector_id`),
    CONSTRAINT `webmail__connector_imapfolder_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email` (
    `email_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `identity_id` int(11) DEFAULT NULL,
    `connector_id` int(11) DEFAULT NULL,
    `connector_imapfolder_id` int(11) DEFAULT NULL,
    `attributes` int(11) DEFAULT NULL,
    `message_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `spam` tinyint(1) DEFAULT NULL,
    `incoming` tinyint(1) DEFAULT NULL,
    `from_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `from_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `text_content` mediumtext COLLATE utf8mb4_general_ci,
    `received` datetime DEFAULT NULL,
    `deleted` datetime DEFAULT NULL,
    `status` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `search_id` bigint(20) DEFAULT NULL,
    PRIMARY KEY (`email_id`),
    KEY `webmail__email_ibfk_1` (`user_id`),
    KEY `id_search_id` (`search_id`),
    KEY `connector_imapfolder_id` (`connector_imapfolder_id`),
    FULLTEXT KEY `text_content` (`text_content`),
    CONSTRAINT `webmail__email_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email_tag` (
    `email_tag_id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `visible` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`email_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email_email_tag` (
    `email_email_tag_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `email_id` int(11) DEFAULT NULL,
    `email_tag_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`email_email_tag_id`),
    UNIQUE KEY `uc_email_id_tag_id` (`email_id`,`email_tag_id`),
    KEY `webmail__email_email_tag_ibfk_2` (`email_tag_id`),
    CONSTRAINT `webmail__email_email_tag_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `webmail__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT `webmail__email_email_tag_ibfk_2` FOREIGN KEY (`email_tag_id`) REFERENCES `webmail__email_tag` (`email_tag_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email_file` (
    `email_file_id` int(11) NOT NULL AUTO_INCREMENT,
    `email_id` int(11) DEFAULT NULL,
    `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    PRIMARY KEY (`email_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email_status` (
    `email_status_id` int(11) NOT NULL AUTO_INCREMENT,
    `status_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `default_selected` tinyint(1) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `visible` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`email_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__email_to` (
    `email_to_id` bigint(20) NOT NULL AUTO_INCREMENT,
    `email_id` int(11) DEFAULT NULL,
    `to_type` enum('To','Cc','Bcc') COLLATE utf8mb4_general_ci DEFAULT NULL,
    `to_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `to_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    PRIMARY KEY (`email_to_id`),
    KEY `webmail__email_to_ibfk_1` (`email_id`),
    CONSTRAINT `webmail__email_to_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `webmail__email` (`email_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__filter` (
    `filter_id` int(11) NOT NULL AUTO_INCREMENT,
    `connector_id` int(11) DEFAULT NULL,
    `filter_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `match_method` enum('match_all','match_one') COLLATE utf8mb4_general_ci DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `active` tinyint(1) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__filter_action` (
    `filter_action_id` int(11) NOT NULL AUTO_INCREMENT,
    `filter_id` int(11) DEFAULT NULL,
    `filter_action` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `filter_action_property` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `filter_action_value` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`filter_action_id`),
    KEY `webmail__filter_action_ibfk_1` (`filter_id`),
    CONSTRAINT `webmail__filter_action_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__filter_condition` (
    `filter_condition_id` int(11) NOT NULL AUTO_INCREMENT,
    `filter_id` int(11) DEFAULT NULL,
    `filter_field` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `filter_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `filter_pattern` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`filter_condition_id`),
    KEY `webmail__filter_condition_ibfk_1` (`filter_id`),
    CONSTRAINT `webmail__filter_condition_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `webmail__identity` (
    `identity_id` int(11) NOT NULL AUTO_INCREMENT,
    `connector_id` int(11) DEFAULT NULL,
    `from_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `from_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `active` tinyint(1) DEFAULT NULL,
    `sort` int(11) DEFAULT '0',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`identity_id`),
    KEY `webmail__identity_ibfk_1` (`connector_id`),
    CONSTRAINT `webmail__identity_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";


$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}



