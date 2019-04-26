<?php


// !!! if DEBUG == true, default password for all accounts is 'timbo123' !!!
define('DEBUG', true);

// standalone installation? or multi-administration mode?
define('STANDALONE_INSTALLATION', true);

// used in multi-administration mode for autologin from the admin-module
define('API_KEY', 'APIKEY1234567890YEKIPA');


if (DEBUG) {
    ini_set('display_errors', 'on');
    ini_set('error_reporting', E_ALL);
}


define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']);
define('BASE_HREF', '/');


// note, not to be used directly, use Context::getInstance()->getDataDir();
define('DATA_DIR', '/projects/peopleweb-php/data');
define('WEBMAIL_SOLR', 'http://localhost:8984/solr/insights');


// used @ core\lib\AuthFilter
define('DEFAULT_DATABASE_HOST',     'localhost');
define('DEFAULT_DATABASE_USERNAME', 'root');
define('DEFAULT_DATABASE_PASSWORD', 'uiz123');

// master-data database
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('admin', 'localhost', 'root', 'uiz123', 'pw_master');

if (php_sapi_name() == 'cli') {
    $dh->addServer('default', 'localhost', 'root', 'uiz', 'insights_dev');
}

