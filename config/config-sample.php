<?php


// 
define('DEBUG', true);

// when DEBUG_PASSWORD is set, all accounts can be used with this password
define('DEBUG_PASSWORD', 'pass123');


// standalone installation? or multi-administration mode?
define('STANDALONE_INSTALLATION', true);

// used in multi-administration mode for autologin from the admin-module
// change this key to something random, else it can be abused
define('API_KEY', 'APIKEY1234567890YEKIPA');


// define('SOFFICE_BIN', 'C:\\Program Files\\LibreOffice\\program\\soffice.exe');

if (DEBUG) {
    ini_set('display_errors', 'on');
    ini_set('error_reporting', E_ALL);
}


define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']);
define('BASE_HREF', '/');


// note, not to be used directly, use Context::getInstance()->getDataDir();
define('DATA_DIR', '/projects/toolbox/data');
define('WEBMAIL_SOLR', 'http://localhost:8984/solr/webmail');


// used @ core\lib\AuthFilter
define('DEFAULT_DATABASE_HOST',     'localhost');
define('DEFAULT_DATABASE_USERNAME', 'root');
define('DEFAULT_DATABASE_PASSWORD', 'uiz123');
define('DEFAULT_DATABASE_NAME',     'toolbox_master');

//ctx()->addModuleDir('/projects/extra-modules');


// master-data database
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('admin', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, DEFAULT_DATABASE_NAME);

if (php_sapi_name() == 'cli') {
    $dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, DEFAULT_DATABASE_NAME);
}

