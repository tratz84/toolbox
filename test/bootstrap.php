<?php

chdir(dirname(__FILE__));

require_once '../config/config.php';
require_once 'functions.php';

bootstrapContext('dev');

// connect to database for current context
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, \core\Context::getInstance()->getCustomer()->getDatabaseName());

