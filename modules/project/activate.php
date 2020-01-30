<?php

use core\db\DatabaseHandler;

$sql = array();


$sql[] = "CREATE TABLE `project__project` (
    `project_id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `project_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `active` tinyint(1) DEFAULT '1',
    `note` longtext COLLATE utf8mb4_general_ci,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`project_id`),
    KEY `person_id` (`person_id`),
    CONSTRAINT `project__project_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE `project__project_hour_type` (
    `project_hour_type_id` int(11) NOT NULL AUTO_INCREMENT,
    `description` longtext COLLATE utf8mb4_general_ci,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `visible` tinyint(1) DEFAULT '1',
    `default_selected` tinyint(1) DEFAULT '0',
    `sort` int(11) DEFAULT NULL,
    PRIMARY KEY (`project_hour_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE `project__project_hour` (
    `project_hour_id` int(11) NOT NULL AUTO_INCREMENT,
    `project_id` int(11) DEFAULT NULL,
    `project_hour_type_id` int(11) DEFAULT NULL,
    `project_hour_status_id` int(11) DEFAULT NULL,
    `registration_type` enum('from_to','duration') COLLATE utf8mb4_general_ci DEFAULT 'from_to',
    `short_description` longtext COLLATE utf8mb4_general_ci,
    `long_description` longtext COLLATE utf8mb4_general_ci,
    `start_time` datetime DEFAULT NULL,
    `end_time` datetime DEFAULT NULL,
    `duration` double DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `declarable` tinyint(1) DEFAULT '1',
    PRIMARY KEY (`project_hour_id`),
    KEY `project_id` (`project_id`),
    KEY `project_hour_type_id` (`project_hour_type_id`),
    CONSTRAINT `project__project_hour_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project__project` (`project_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `project__project_hour_ibfk_2` FOREIGN KEY (`project_hour_type_id`) REFERENCES `project__project_hour_type` (`project_hour_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE `project__project_hour_status` (
    `project_hour_status_id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `default_selected` tinyint(1) DEFAULT '0',
    `sort` int(11) DEFAULT NULL,
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`project_hour_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";



$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}


