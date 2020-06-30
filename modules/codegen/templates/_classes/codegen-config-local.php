<?= "<?php" ?>


// 
define('DEBUG', false);
define('DEBUG_PASSWORD', '');

// standalone installation? or multi-administration mode?
define('STANDALONE_INSTALLATION', true);

// used in multi-administration mode for autologin from the admin-module
define('API_KEY', <?= var_export($api_key) ?>);

if (DEBUG) {
    ini_set('display_errors', 'on');
    ini_set('error_reporting', E_ALL);
}


define('BASE_URL', '<?= @$_SERVER['HTTPS']?'https':'http' ?>://<?= $_SERVER['HTTP_HOST'] ?>');
define('BASE_HREF', <?= var_export($base_href) ?>);


// note, not to be used directly, use Context::getInstance()->getDataDir();
define('DATA_DIR', <?= var_export($data_dir) ?>);
// define('WEBMAIL_SOLR', 'http://localhost:8984/solr/insights');


// used @ core\lib\AuthFilter
define('DEFAULT_DATABASE_HOST',     <?= var_export($db_host) ?>);
define('DEFAULT_DATABASE_USERNAME', <?= var_export($db_user) ?>);
define('DEFAULT_DATABASE_PASSWORD', <?= var_export($db_password) ?>);
define('DEFAULT_DATABASE_NAME',     <?= var_export($db_name) ?>);

// master-data database
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('admin', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, DEFAULT_DATABASE_NAME);

if (php_sapi_name() == 'cli') {
    $dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, DEFAULT_DATABASE_NAME);
}

