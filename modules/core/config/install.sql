
CREATE TABLE IF NOT EXISTS `insights__autologin` (
  `autologin_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `securityString` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastUsed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`autologin_id`),
  UNIQUE KEY `contextName` (`contextName`,`securityString`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
CREATE TABLE IF NOT EXISTS `insights__customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `databaseName` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `experimental` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `contextName` (`contextName`),
  UNIQUE KEY `databaseName` (`databaseName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `insights__exception_log` (
  `exception_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_uri` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stacktrace` mediumtext COLLATE utf8mb4_general_ci,
  `parameters` mediumtext COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`exception_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `insights__user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `insights__user_customer` (
  `user_customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

