

INSERT INTO `base__setting` VALUES (NULL,NULL,'PAGE_SIZE',NULL,NULL,'15');
INSERT INTO `base__setting` VALUES (NULL,NULL,'personsEnabled',NULL,NULL,'0');
INSERT INTO `base__setting` VALUES (NULL,NULL,'SQL_VERSION',NULL,NULL,'2019042601');

-- create user admin, with password test123
INSERT INTO `base__user` VALUES (NULL, 'admin', 'demo@demo.itxplain.nl', 'test123', now(), now(), 'admin', '', 'demo', NULL, NULL);
-- INSERT INTO `insights__customer` set contextName='default', databaseName='insights_github', active=true;



