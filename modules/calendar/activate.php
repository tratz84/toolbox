<?php

use core\db\DatabaseHandler;

$sql = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__calendar` (
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `secret_key` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_id`),
  KEY `cal__calendar_ibfk_1` (`user_id`),
  CONSTRAINT `cal__calendar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__calendar_item_status` (
  `calendar_item_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__calendar_item_category` (
  `calendar_item_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__calendar_item` (
  `calendar_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_calendar_item_id` int(11) DEFAULT NULL,
  `calendar_id` int(11) DEFAULT NULL,
  `calendar_item_status_id` int(11) DEFAULT NULL,
  `calendar_item_category_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT NULL,
  `private` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reminder` int(11) DEFAULT NULL,
  `recurrence_type` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recurrence_rule` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `exdate` longtext COLLATE utf8mb4_general_ci,
  `cancelled` tinyint(1) DEFAULT '0',
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_id`),
  KEY `cal__calendar_item_ibfk_1` (`calendar_item_status_id`),
  KEY `cal__calendar_item_ibfk_2` (`calendar_item_category_id`),
  CONSTRAINT `cal__calendar_item_ibfk_1` FOREIGN KEY (`calendar_item_status_id`) REFERENCES `cal__calendar_item_status` (`calendar_item_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cal__calendar_item_ibfk_2` FOREIGN KEY (`calendar_item_category_id`) REFERENCES `cal__calendar_item_category` (`calendar_item_category_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__todo` (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `list_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cal__todo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `cal__todo_item` (
  `todo_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `todo_id` int(11) DEFAULT NULL,
  `summary` varchar(512) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `long_description` longtext COLLATE utf8mb4_general_ci,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`todo_item_id`),
  KEY `todo_id` (`todo_id`),
  CONSTRAINT `cal__todo_item_ibfk_1` FOREIGN KEY (`todo_id`) REFERENCES `cal__todo` (`todo_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";



$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}



