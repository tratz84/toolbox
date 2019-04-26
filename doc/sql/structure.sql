-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: localhost    Database: insights_demo
-- ------------------------------------------------------
-- Server version	5.7.25

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `article__article`
--

DROP TABLE IF EXISTS `article__article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article__article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_type` varchar(32) DEFAULT NULL,
  `article_name` varchar(255) DEFAULT NULL,
  `long_description1` mediumtext,
  `long_description2` mediumtext,
  `price` double DEFAULT NULL,
  `rentable` tinyint(1) DEFAULT '0',
  `simultaneously_rentable` int(11) DEFAULT NULL,
  `price_type` varchar(16) DEFAULT NULL,
  `vat_price` bigint(20) DEFAULT NULL,
  `vat_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `article__article_ibfk_1` (`vat_id`),
  CONSTRAINT `article__article_ibfk_1` FOREIGN KEY (`vat_id`) REFERENCES `invoice__vat` (`vat_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article__article_article_group`
--

DROP TABLE IF EXISTS `article__article_article_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article__article_article_group` (
  `article_group_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`article_group_id`,`article_id`),
  KEY `article__article_article_group_ibfk_2` (`article_id`),
  CONSTRAINT `article__article_article_group_ibfk_1` FOREIGN KEY (`article_group_id`) REFERENCES `article__article_group` (`article_group_id`) ON DELETE CASCADE,
  CONSTRAINT `article__article_article_group_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article__article` (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article__article_group`
--

DROP TABLE IF EXISTS `article__article_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article__article_group` (
  `article_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_article_group_id` int(11) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `long_description1` mediumtext,
  `long_description2` mediumtext,
  `active` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`article_group_id`),
  KEY `article__article_group_ibfk_1` (`parent_article_group_id`),
  CONSTRAINT `article__article_group_ibfk_1` FOREIGN KEY (`parent_article_group_id`) REFERENCES `article__article_group` (`article_group_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__activity`
--

DROP TABLE IF EXISTS `base__activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `ref_object` varchar(32) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `code` varchar(32) DEFAULT NULL,
  `short_description` text,
  `long_description` text,
  `note` text,
  `changes` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__cron`
--

DROP TABLE IF EXISTS `base__cron`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__cron` (
  `cron_id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_name` varchar(128) DEFAULT NULL,
  `last_status` varchar(128) DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `running` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__cron_run`
--

DROP TABLE IF EXISTS `base__cron_run`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__cron_run` (
  `cron_run_id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) DEFAULT NULL,
  `message` text,
  `error` text,
  `status` varchar(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`cron_run_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__file`
--

DROP TABLE IF EXISTS `base__file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_count` int(11) DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `module_name` varchar(128) DEFAULT NULL,
  `category_name` varchar(128) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__menu`
--

DROP TABLE IF EXISTS `base__menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(64) DEFAULT NULL,
  `parent_menu_code` varchar(64) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`menu_id`),
  UNIQUE KEY `menu_code` (`menu_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__multiuser_lock`
--

DROP TABLE IF EXISTS `base__multiuser_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__multiuser_lock` (
  `username` varchar(128) NOT NULL,
  `tabuid` varchar(48) NOT NULL,
  `lock_key` varchar(255) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`username`,`tabuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__object_meta`
--

DROP TABLE IF EXISTS `base__object_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__object_meta` (
  `object_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(128) DEFAULT NULL,
  `object_key` varchar(128) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_value` longtext,
  `object_note` text,
  PRIMARY KEY (`object_meta_id`),
  UNIQUE KEY `index_key_id` (`object_name`,`object_key`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__reset_password`
--

DROP TABLE IF EXISTS `base__reset_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__reset_password` (
  `reset_password_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(128) DEFAULT NULL,
  `security_string` varchar(128) DEFAULT NULL,
  `request_ip` varchar(64) DEFAULT NULL,
  `used_ip` varchar(64) DEFAULT NULL,
  `used` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`reset_password_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__setting`
--

DROP TABLE IF EXISTS `base__setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__setting` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_type` varchar(16) DEFAULT NULL,
  `setting_code` varchar(64) DEFAULT NULL,
  `short_description` longtext,
  `long_description` longtext,
  `text_value` longtext,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_code` (`setting_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__user`
--

DROP TABLE IF EXISTS `base__user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `firstname` varchar(128) DEFAULT NULL,
  `lastname` varchar(128) DEFAULT NULL,
  `autologin_token` varchar(255) DEFAULT NULL,
  `activated` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__user_capability`
--

DROP TABLE IF EXISTS `base__user_capability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__user_capability` (
  `user_capability_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module_name` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `capability_code` varchar(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_capability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `base__user_ip`
--

DROP TABLE IF EXISTS `base__user_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `base__user_ip` (
  `user_ip_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`user_ip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__calendar`
--

DROP TABLE IF EXISTS `cal__calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__calendar` (
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `secret_key` varchar(64) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_id`),
  KEY `cal__calendar_ibfk_1` (`user_id`),
  CONSTRAINT `cal__calendar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__calendar_item`
--

DROP TABLE IF EXISTS `cal__calendar_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__calendar_item` (
  `calendar_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_calendar_item_id` int(11) DEFAULT NULL,
  `calendar_id` int(11) DEFAULT NULL,
  `calendar_item_status_id` int(11) DEFAULT NULL,
  `calendar_item_category_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `all_day` tinyint(1) DEFAULT NULL,
  `private` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reminder` int(11) DEFAULT NULL,
  `recurrence_type` varchar(16) DEFAULT NULL,
  `recurrence_rule` varchar(255) DEFAULT NULL,
  `message` text,
  `exdate` longtext,
  `cancelled` tinyint(1) DEFAULT '0',
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_id`),
  KEY `cal__calendar_item_ibfk_1` (`calendar_item_status_id`),
  KEY `cal__calendar_item_ibfk_2` (`calendar_item_category_id`),
  CONSTRAINT `cal__calendar_item_ibfk_1` FOREIGN KEY (`calendar_item_status_id`) REFERENCES `cal__calendar_item_status` (`calendar_item_status_id`),
  CONSTRAINT `cal__calendar_item_ibfk_2` FOREIGN KEY (`calendar_item_category_id`) REFERENCES `cal__calendar_item_category` (`calendar_item_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__calendar_item_category`
--

DROP TABLE IF EXISTS `cal__calendar_item_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__calendar_item_category` (
  `calendar_item_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__calendar_item_status`
--

DROP TABLE IF EXISTS `cal__calendar_item_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__calendar_item_status` (
  `calendar_item_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`calendar_item_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__todo`
--

DROP TABLE IF EXISTS `cal__todo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__todo` (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `list_name` varchar(255) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cal__todo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal__todo_item`
--

DROP TABLE IF EXISTS `cal__todo_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal__todo_item` (
  `todo_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `todo_id` int(11) DEFAULT NULL,
  `summary` varchar(512) DEFAULT NULL,
  `long_description` longtext,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`todo_item_id`),
  KEY `todo_id` (`todo_id`),
  CONSTRAINT `cal__todo_item_ibfk_1` FOREIGN KEY (`todo_id`) REFERENCES `cal__todo` (`todo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__address`
--

DROP TABLE IF EXISTS `customer__address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `street` varchar(255) DEFAULT NULL,
  `street_no` varchar(64) DEFAULT NULL,
  `zipcode` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `note` longtext,
  `sort` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `customer__address_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `customer__country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__company`
--

DROP TABLE IF EXISTS `customer__company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(128) DEFAULT NULL,
  `coc_number` varchar(128) DEFAULT NULL,
  `vat_number` varchar(64) DEFAULT NULL,
  `iban` varchar(64) DEFAULT NULL,
  `bic` varchar(32) DEFAULT NULL,
  `note` longtext,
  `deleted` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `company_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  KEY `company_type_id` (`company_type_id`),
  CONSTRAINT `customer__company_ibfk_1` FOREIGN KEY (`company_type_id`) REFERENCES `customer__company_type` (`company_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__company_address`
--

DROP TABLE IF EXISTS `customer__company_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__company_address` (
  `company_address_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_address_id`),
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`),
  CONSTRAINT `customer__company_address_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`),
  CONSTRAINT `customer__company_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__company_email`
--

DROP TABLE IF EXISTS `customer__company_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__company_email` (
  `company_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_email_id`),
  KEY `company_id` (`company_id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `customer__company_email_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`),
  CONSTRAINT `customer__company_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__company_phone`
--

DROP TABLE IF EXISTS `customer__company_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__company_phone` (
  `company_phone_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_phone_id`),
  KEY `company_id` (`company_id`),
  KEY `phone_id` (`phone_id`),
  CONSTRAINT `customer__company_phone_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`),
  CONSTRAINT `customer__company_phone_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__company_type`
--

DROP TABLE IF EXISTS `customer__company_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__company_type` (
  `company_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(64) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`company_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__country`
--

DROP TABLE IF EXISTS `customer__country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `country_iso2` varchar(2) DEFAULT NULL,
  `country_iso3` varchar(3) DEFAULT NULL,
  `country_no` varchar(3) DEFAULT NULL,
  `phone_prefix` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__email`
--

DROP TABLE IF EXISTS `customer__email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__email` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `description` longtext,
  `primary_address` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__person`
--

DROP TABLE IF EXISTS `customer__person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__person` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(128) DEFAULT NULL,
  `insert_lastname` varchar(32) DEFAULT NULL,
  `lastname` varchar(128) DEFAULT NULL,
  `iban` varchar(64) DEFAULT NULL,
  `bic` varchar(32) DEFAULT NULL,
  `note` longtext,
  `deleted` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__person_address`
--

DROP TABLE IF EXISTS `customer__person_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__person_address` (
  `person_address_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`person_address_id`),
  KEY `person_id` (`person_id`),
  KEY `address_id` (`address_id`),
  CONSTRAINT `customer__person_address_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`),
  CONSTRAINT `customer__person_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer__address` (`address_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__person_email`
--

DROP TABLE IF EXISTS `customer__person_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__person_email` (
  `person_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`person_email_id`),
  KEY `person_id` (`person_id`),
  KEY `email_id` (`email_id`),
  CONSTRAINT `customer__person_email_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`),
  CONSTRAINT `customer__person_email_ibfk_3` FOREIGN KEY (`email_id`) REFERENCES `customer__email` (`email_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__person_phone`
--

DROP TABLE IF EXISTS `customer__person_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__person_phone` (
  `person_phone_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `phone_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`person_phone_id`),
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`phone_id`),
  CONSTRAINT `customer__person_phone_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`),
  CONSTRAINT `customer__person_phone_ibfk_2` FOREIGN KEY (`phone_id`) REFERENCES `customer__phone` (`phone_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer__phone`
--

DROP TABLE IF EXISTS `customer__phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer__phone` (
  `phone_id` int(11) NOT NULL AUTO_INCREMENT,
  `phonenr` varchar(128) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `note` longtext,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`phone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesync__store`
--

DROP TABLE IF EXISTS `filesync__store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesync__store` (
  `store_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_type` varchar(16) DEFAULT NULL,
  `store_name` varchar(128) DEFAULT NULL,
  `note` mediumtext,
  `last_file_change` bigint(20) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `store_name` (`store_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesync__store_file`
--

DROP TABLE IF EXISTS `filesync__store_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesync__store_file` (
  `store_file_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rev` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`store_file_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `filesync__store_file_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `filesync__store` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesync__store_file_meta`
--

DROP TABLE IF EXISTS `filesync__store_file_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesync__store_file_meta` (
  `store_file_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `store_file_id` bigint(20) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `long_description` text,
  `document_date` date DEFAULT NULL,
  PRIMARY KEY (`store_file_meta_id`),
  UNIQUE KEY `store_file_id` (`store_file_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `filesync__store_file_meta_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`),
  CONSTRAINT `filesync__store_file_meta_ibfk_2` FOREIGN KEY (`store_file_id`) REFERENCES `filesync__store_file` (`store_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesync__store_file_meta_tag`
--

DROP TABLE IF EXISTS `filesync__store_file_meta_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesync__store_file_meta_tag` (
  `meta_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`meta_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filesync__store_file_rev`
--

DROP TABLE IF EXISTS `filesync__store_file_rev`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filesync__store_file_rev` (
  `store_file_rev_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `store_file_id` bigint(20) DEFAULT NULL,
  `filesize` bigint(20) DEFAULT NULL,
  `md5sum` varchar(32) DEFAULT NULL,
  `rev` int(11) DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  `encrypted` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`store_file_rev_id`),
  UNIQUE KEY `uc_store_file_id_rev` (`store_file_id`,`rev`),
  CONSTRAINT `filesync__store_file_rev_ibfk_1` FOREIGN KEY (`store_file_id`) REFERENCES `filesync__store_file` (`store_file_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__company_setting`
--

DROP TABLE IF EXISTS `invoice__company_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__company_setting` (
  `company_setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `tax_shift` tinyint(1) DEFAULT '0',
  `tax_excemption` tinyint(1) DEFAULT '0',
  `payment_term` int(11) DEFAULT NULL,
  PRIMARY KEY (`company_setting_id`),
  UNIQUE KEY `company_id` (`company_id`),
  CONSTRAINT `invoice__company_setting_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `customer__company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__invoice`
--

DROP TABLE IF EXISTS `invoice__invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `invoice_status_id` int(11) DEFAULT NULL,
  `tax_shift` tinyint(1) DEFAULT '0',
  `invoice_number` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `comment` mediumtext,
  `note` text,
  `total_calculated_price` decimal(10,2) DEFAULT NULL,
  `total_calculated_price_incl_vat` decimal(10,2) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `invoice__invoice_ibfk_1` (`invoice_status_id`),
  CONSTRAINT `invoice__invoice_ibfk_1` FOREIGN KEY (`invoice_status_id`) REFERENCES `invoice__invoice_status` (`invoice_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__invoice_line`
--

DROP TABLE IF EXISTS `invoice__invoice_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__invoice_line` (
  `invoice_line_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `vat_percentage` double DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`invoice_line_id`),
  KEY `invoice__invoice_line_ibfk_1` (`invoice_id`),
  CONSTRAINT `invoice__invoice_line_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice__invoice` (`invoice_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__invoice_status`
--

DROP TABLE IF EXISTS `invoice__invoice_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__invoice_status` (
  `invoice_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoice_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__offer`
--

DROP TABLE IF EXISTS `invoice__offer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__offer` (
  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `offer_number` varchar(16) DEFAULT NULL,
  `offer_status_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `comment` mediumtext,
  `note` text,
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
  CONSTRAINT `invoice__offer_ibfk_1` FOREIGN KEY (`offer_status_id`) REFERENCES `invoice__offer_status` (`offer_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__offer_file`
--

DROP TABLE IF EXISTS `invoice__offer_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__offer_file` (
  `offer_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`offer_file_id`),
  UNIQUE KEY `uq_offer_id_file_id` (`offer_id`,`file_id`),
  KEY `file_id` (`file_id`),
  CONSTRAINT `invoice__offer_file_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `base__file` (`file_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__offer_line`
--

DROP TABLE IF EXISTS `invoice__offer_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__offer_line` (
  `offer_line_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `short_description2` varchar(255) DEFAULT NULL,
  `long_description` mediumtext,
  `amount` double DEFAULT NULL,
  `price` double DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `line_type` varchar(16) DEFAULT 'price',
  `sort` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`offer_line_id`),
  KEY `offer_id` (`offer_id`),
  CONSTRAINT `invoice__offer_line_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `invoice__offer` (`offer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__offer_status`
--

DROP TABLE IF EXISTS `invoice__offer_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__offer_status` (
  `offer_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`offer_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__payment`
--

DROP TABLE IF EXISTS `invoice__payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_object` varchar(32) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `invoice_line_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `note` text,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_type` varchar(32) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__payment_method`
--

DROP TABLE IF EXISTS `invoice__payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__payment_method` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `note` text,
  `sort` int(11) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_method_id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__price_adjustment`
--

DROP TABLE IF EXISTS `invoice__price_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__price_adjustment` (
  `price_adjustment_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `ref_object` varchar(32) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `new_discount` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `executed` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`price_adjustment_id`),
  KEY `ref_object` (`ref_object`,`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__to_bill`
--

DROP TABLE IF EXISTS `invoice__to_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__to_bill` (
  `to_bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
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
  CONSTRAINT `invoice__to_bill_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project__project` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `invoice__to_bill_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `invoice__to_bill_ibfk_4` FOREIGN KEY (`invoice_line_id`) REFERENCES `invoice__invoice_line` (`invoice_line_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice__vat`
--

DROP TABLE IF EXISTS `invoice__vat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice__vat` (
  `vat_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  `default_selected` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`vat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing__log`
--

DROP TABLE IF EXISTS `mailing__log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailing__log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `log_to` text,
  `log_cc` text,
  `log_bcc` text,
  `subject` varchar(512) DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing__template`
--

DROP TABLE IF EXISTS `mailing__template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailing__template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_code` varchar(64) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `subject` varchar(512) DEFAULT NULL,
  `content` text,
  `active` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `template_code` (`template_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailing__template_to`
--

DROP TABLE IF EXISTS `mailing__template_to`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailing__template_to` (
  `template_to_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `to_type` enum('To','Cc','Bcc') DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`template_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project__project`
--

DROP TABLE IF EXISTS `project__project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project__project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `note` longtext,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `project__project_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `customer__person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project__project_hour`
--

DROP TABLE IF EXISTS `project__project_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project__project_hour` (
  `project_hour_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `project_hour_type_id` int(11) DEFAULT NULL,
  `project_hour_status_id` int(11) DEFAULT NULL,
  `registration_type` enum('from_to','duration') DEFAULT 'from_to',
  `short_description` longtext,
  `long_description` longtext,
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
  CONSTRAINT `project__project_hour_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project__project` (`project_id`),
  CONSTRAINT `project__project_hour_ibfk_2` FOREIGN KEY (`project_hour_type_id`) REFERENCES `project__project_hour_type` (`project_hour_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project__project_hour_status`
--

DROP TABLE IF EXISTS `project__project_hour_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project__project_hour_status` (
  `project_hour_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(64) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`project_hour_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project__project_hour_type`
--

DROP TABLE IF EXISTS `project__project_hour_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project__project_hour_type` (
  `project_hour_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` longtext,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  `default_selected` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`project_hour_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `signrequest__message`
--

DROP TABLE IF EXISTS `signrequest__message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signrequest__message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_object` varchar(32) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `message` text,
  `documents_response` text,
  `signrequests_response` text,
  `sent` tinyint(1) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `signrequest__message_file`
--

DROP TABLE IF EXISTS `signrequest__message_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signrequest__message_file` (
  `message_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`message_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `signrequest__message_signer`
--

DROP TABLE IF EXISTS `signrequest__message_signer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signrequest__message_signer` (
  `message_signer_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) DEFAULT NULL,
  `signer_email` varchar(255) DEFAULT NULL,
  `signer_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`message_signer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status__currentstate`
--

DROP TABLE IF EXISTS `status__currentstate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status__currentstate` (
  `currentstate_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(128) DEFAULT NULL,
  `process_name` varchar(128) DEFAULT NULL,
  `description` mediumtext,
  `state` varchar(32) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`currentstate_id`),
  UNIQUE KEY `module_name` (`module_name`,`process_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status__log`
--

DROP TABLE IF EXISTS `status__log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status__log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) DEFAULT NULL,
  `process_name` varchar(255) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task__item`
--

DROP TABLE IF EXISTS `task__item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task__item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_code` varchar(64) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `lastrun` datetime DEFAULT NULL,
  `last_status` varchar(512) DEFAULT NULL,
  `last_status_time` datetime DEFAULT NULL,
  `last_error` varchar(512) DEFAULT NULL,
  `last_error_time` datetime DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task__item_log`
--

DROP TABLE IF EXISTS `task__item_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task__item_log` (
  `item_log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `log_code` varchar(32) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`item_log_id`),
  KEY `task__item_log_ibfk_1` (`item_id`),
  CONSTRAINT `task__item_log_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `task__item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__connector`
--

DROP TABLE IF EXISTS `webmail__connector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__connector` (
  `connector_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `connector_type` varchar(16) DEFAULT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nextrun_fullimport` tinyint(1) DEFAULT NULL,
  `sent_connector_imapfolder_id` int(11) DEFAULT NULL,
  `junk_connector_imapfolder_id` int(11) DEFAULT NULL,
  `trash_connector_imapfolder_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`connector_id`),
  KEY `email__connector_ibfk_1` (`user_id`),
  CONSTRAINT `email__connector_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__connector_imapfolder`
--

DROP TABLE IF EXISTS `webmail__connector_imapfolder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__connector_imapfolder` (
  `connector_imapfolder_id` int(11) NOT NULL AUTO_INCREMENT,
  `connector_id` int(11) DEFAULT NULL,
  `folderName` varchar(255) DEFAULT NULL,
  `attributes` int(11) DEFAULT NULL,
  `outgoing` tinyint(1) DEFAULT NULL,
  `junk` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`connector_imapfolder_id`),
  KEY `webmail__connector_imapfolder_ibfk_1` (`connector_id`),
  CONSTRAINT `webmail__connector_imapfolder_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email`
--

DROP TABLE IF EXISTS `webmail__email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `identity_id` int(11) DEFAULT NULL,
  `connector_id` int(11) DEFAULT NULL,
  `connector_imapfolder_id` int(11) DEFAULT NULL,
  `attributes` int(11) DEFAULT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `spam` tinyint(1) DEFAULT NULL,
  `incoming` tinyint(1) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `text_content` mediumtext,
  `received` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `search_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`email_id`),
  KEY `webmail__email_ibfk_1` (`user_id`),
  KEY `id_search_id` (`search_id`),
  KEY `connector_imapfolder_id` (`connector_imapfolder_id`),
  FULLTEXT KEY `text_content` (`text_content`),
  CONSTRAINT `webmail__email_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `base__user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email_email_tag`
--

DROP TABLE IF EXISTS `webmail__email_email_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email_email_tag` (
  `email_email_tag_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) DEFAULT NULL,
  `email_tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`email_email_tag_id`),
  UNIQUE KEY `uc_email_id_tag_id` (`email_id`,`email_tag_id`),
  KEY `webmail__email_email_tag_ibfk_2` (`email_tag_id`),
  CONSTRAINT `webmail__email_email_tag_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `webmail__email` (`email_id`) ON DELETE CASCADE,
  CONSTRAINT `webmail__email_email_tag_ibfk_2` FOREIGN KEY (`email_tag_id`) REFERENCES `webmail__email_tag` (`email_tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email_file`
--

DROP TABLE IF EXISTS `webmail__email_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email_file` (
  `email_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email_status`
--

DROP TABLE IF EXISTS `webmail__email_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email_status` (
  `email_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(255) DEFAULT NULL,
  `default_selected` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`email_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email_tag`
--

DROP TABLE IF EXISTS `webmail__email_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email_tag` (
  `email_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`email_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__email_to`
--

DROP TABLE IF EXISTS `webmail__email_to`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__email_to` (
  `email_to_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_id` int(11) DEFAULT NULL,
  `to_type` enum('To','Cc','Bcc') DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email_to_id`),
  KEY `webmail__email_to_ibfk_1` (`email_id`),
  CONSTRAINT `webmail__email_to_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `webmail__email` (`email_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__filter`
--

DROP TABLE IF EXISTS `webmail__filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__filter` (
  `filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `connector_id` int(11) DEFAULT NULL,
  `filter_name` varchar(255) DEFAULT NULL,
  `match_method` enum('match_all','match_one') DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__filter_action`
--

DROP TABLE IF EXISTS `webmail__filter_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__filter_action` (
  `filter_action_id` int(11) NOT NULL AUTO_INCREMENT,
  `filter_id` int(11) DEFAULT NULL,
  `filter_action` varchar(255) DEFAULT NULL,
  `filter_action_property` varchar(255) DEFAULT NULL,
  `filter_action_value` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`filter_action_id`),
  KEY `webmail__filter_action_ibfk_1` (`filter_id`),
  CONSTRAINT `webmail__filter_action_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__filter_condition`
--

DROP TABLE IF EXISTS `webmail__filter_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__filter_condition` (
  `filter_condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `filter_id` int(11) DEFAULT NULL,
  `filter_field` varchar(255) DEFAULT NULL,
  `filter_type` varchar(255) DEFAULT NULL,
  `filter_pattern` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`filter_condition_id`),
  KEY `webmail__filter_condition_ibfk_1` (`filter_id`),
  CONSTRAINT `webmail__filter_condition_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `webmail__filter` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail__identity`
--

DROP TABLE IF EXISTS `webmail__identity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail__identity` (
  `identity_id` int(11) NOT NULL AUTO_INCREMENT,
  `connector_id` int(11) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `sort` int(11) DEFAULT '0',
  `edited` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`identity_id`),
  KEY `webmail__identity_ibfk_1` (`connector_id`),
  CONSTRAINT `webmail__identity_ibfk_1` FOREIGN KEY (`connector_id`) REFERENCES `webmail__connector` (`connector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-23  8:28:33
