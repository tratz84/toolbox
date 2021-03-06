<?php

use core\Context;

define('ROOT', realpath( dirname(__FILE__) . '/..' ));

// define('SQL_VERSION', 2019051401);

require_once dirname(__FILE__).'/../modules/core/lib/Context.php';
require_once dirname(__FILE__).'/../modules/core/lib/autoload.php';

if (file_exists(dirname(__FILE__).'/../vendor/autoload.php')) {
    require_once dirname(__FILE__).'/../vendor/autoload.php';
} else {
    die('Composer packages not installed, run: composer install');
}


Context::getInstance()->addModuleDir( ROOT . '/modules' );

// TODO: remove this
Context::getInstance()->enableModule('base');


$file = dirname(__FILE__).'/config-local.php';
if (file_exists($file)) {
    include $file;
} else {
    define('INSTALLATION_MODE', true);
//     die('Config not found: config-local.php');
}

// maybe make this configurable? Netherlands is main market, so oke for now..
date_default_timezone_set('Europe/Amsterdam');

if (!defined('SALT'))
    define('SALT', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

if (!defined('MULTIUSER_CHECK_INTERVAL'))
    define('MULTIUSER_CHECK_INTERVAL', 10);

if (defined('WWW_ROOT') == false)
    define('WWW_ROOT', realpath(ROOT . '/www'));

if (defined('BASE_HREF') == false)
    define('BASE_HREF', '/');

if (defined('DEBUG') == false)
    define('DEBUG', false);

if (defined('CURRENCY_SYMBOL') == false)
    define('CURRENCY_SYMBOL', '€');

