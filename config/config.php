<?php

use core\Context;

define('ROOT', realpath( dirname(__FILE__) . '/..' ));

define('SQL_VERSION', 2019042502);

define('SALT', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

require_once dirname(__FILE__).'/../modules/core/lib/Context.php';
require_once dirname(__FILE__).'/../modules/core/lib/autoload.php';
require_once dirname(__FILE__).'/../vendor/autoload.php';


Context::getInstance()->addModuleDir( ROOT . '/modules' );
Context::getInstance()->enableModule('base');


$file = dirname(__FILE__).'/config-local.php';
if (file_exists($file)) 
    include $file;
else
    die('Config not found: config-local.php');


if (!defined('MULTIUSER_CHECK_INTERVAL'))
    define('MULTIUSER_CHECK_INTERVAL', 10);

if (defined('WWW_ROOT') == false)
    define('WWW_ROOT', realpath(ROOT . '/www'));

if (defined('BASE_HREF') == false)
    define('BASE_HREF', '/');

if (defined('DEBUG') == false)
    define('DEBUG', false);


