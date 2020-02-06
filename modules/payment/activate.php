<?php


use core\db\DatabaseHandler;

$sql = array();

// $sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment` (
//     `payment_id` int(11) NOT NULL AUTO_INCREMENT,
//     `ref_object` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
//     `ref_id` int(11) DEFAULT NULL,
//     `payment_method_id` int(11) DEFAULT NULL,
//     `person_id` int(11) DEFAULT NULL,
//     `company_id` int(11) DEFAULT NULL,
//     `invoice_id` int(11) DEFAULT NULL,
//     `invoice_line_id` int(11) DEFAULT NULL,
//     `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
//     `note` text COLLATE utf8mb4_general_ci,
//     `amount` decimal(10,2) DEFAULT NULL,
//     `payment_type` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
//     `payment_date` date DEFAULT NULL,
//     `created` datetime DEFAULT NULL,
//     PRIMARY KEY (`payment_id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `cancelled` boolean default false,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment_line` (
  `payment_line_id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int DEFAULT NULL,
  `payment_method_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `bankaccountno` varchar(40) DEFAULT NULL,
  `bankaccountno_contra` varchar(40) DEFAULT NULL,
  `code` varchar(16) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description1` text,
  `description2` text,
  `mutation_type` varchar(64) DEFAULT NULL,
  `sort` int DEFAULT NULL,
  PRIMARY KEY (`payment_line_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment_ref` (
  `payment_ref_id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int DEFAULT NULL,
  `ref_object` varchar(32) DEFAULT NULL,
  `ref_id` int DEFAULT NULL,
  PRIMARY KEY (`payment_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";



$sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment_method` (
    `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `note` text COLLATE utf8mb4_general_ci,
    `sort` int(11) DEFAULT NULL,
    `default_selected` tinyint(1) DEFAULT '0',
    `active` tinyint(1) DEFAULT '1',
    `deleted` tinyint(1) DEFAULT '0',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`payment_method_id`),
    UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS payment__payment_import (
    payment_import_id int primary key auto_increment,
    description varchar(255),
    created datetime
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS payment__payment_import_line (
    payment_import_line_id int primary key auto_increment,
    payment_import_id int,
    debet_credit varchar(1),
    amount decimal(10, 2),
    bankaccountno varchar(64),
    bankaccountno_contra varchar(64),
    payment_date date,
    name varchar(255),
    description varchar(512),
    code varchar(16),
    mutation_type varchar(32),
    company_id int,
    person_id int,
    invoice_id int,
    import_status varchar(16)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";


$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}


