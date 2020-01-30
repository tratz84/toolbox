<?php

use core\db\DatabaseHandler;

$sql = array();



$sql[] = "CREATE TABLE IF NOT EXISTS `signrequest__message` (
    `message_id` int(11) NOT NULL AUTO_INCREMENT,
    `ref_object` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `ref_id` int(11) DEFAULT NULL,
    `from_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `from_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `message` text COLLATE utf8mb4_general_ci,
    `documents_response` text COLLATE utf8mb4_general_ci,
    `signrequests_response` text COLLATE utf8mb4_general_ci,
    `sent` tinyint(1) DEFAULT '0',
    `edited` datetime DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$sql[] = "CREATE TABLE IF NOT EXISTS `signrequest__message_signer` (
    `message_signer_id` int(11) NOT NULL AUTO_INCREMENT,
    `message_id` int(11) DEFAULT NULL,
    `signer_email` varchar(255) DEFAULT NULL,
    `signer_name` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`message_signer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";



$dbh = DatabaseHandler::getConnection('default');
foreach($sql as $s) {
    $dbh->query($s);
}

