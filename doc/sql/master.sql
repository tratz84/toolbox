DROP TABLE IF EXISTS `toolbox__autologin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolbox__autologin` (
  `autologin_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) NOT NULL,
  `securityString` varchar(128) NOT NULL,
  `username` varchar(128) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `lastUsed` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`autologin_id`),
  UNIQUE KEY `contextName` (`contextName`,`securityString`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `toolbox__customer`
--

DROP TABLE IF EXISTS `toolbox__customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolbox__customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) DEFAULT NULL,
  `databaseName` varchar(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `note` text,
  `experimental` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `contextName` (`contextName`),
  UNIQUE KEY `databaseName` (`databaseName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `toolbox__exception_log`
--

DROP TABLE IF EXISTS `toolbox__exception_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolbox__exception_log` (
  `exception_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `contextName` varchar(64) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `stacktrace` mediumtext,
  `parameters` mediumtext,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`exception_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `toolbox__user`
--

DROP TABLE IF EXISTS `toolbox__user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolbox__user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `user_type` varchar(32) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `toolbox__user_customer`
--

DROP TABLE IF EXISTS `toolbox__user_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `toolbox__user_customer` (
  `user_customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
