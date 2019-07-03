CREATE TABLE `base__object_log` (
  `object_log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(128) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_key` varchar(128) DEFAULT NULL,
  `object_action` varchar(8) DEFAULT NULL,
  `value_old` text,
  `value_new` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`object_log_id`),
  KEY `id_name_id` (`object_name`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4