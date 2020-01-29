<?php


$sql = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `payment__payment` (
    `payment_id` int(11) NOT NULL AUTO_INCREMENT,
    `ref_object` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `ref_id` int(11) DEFAULT NULL,
    `payment_method_id` int(11) DEFAULT NULL,
    `person_id` int(11) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `invoice_id` int(11) DEFAULT NULL,
    `invoice_line_id` int(11) DEFAULT NULL,
    `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `note` text COLLATE utf8mb4_general_ci,
    `amount` decimal(10,2) DEFAULT NULL,
    `payment_type` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `payment_date` date DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

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



$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}


