<?php

use core\exception\DatabaseException;
use core\exception\SecurityException;

require_once '../config/config.php';


try {
    $fc = new \core\filter\FilterChain();
    
    $fc->addFilter( new \core\filter\ModulePublicFilter() );
    $fc->addFilter( new \core\filter\SessionFilter() );
    $fc->addFilter( new \core\filter\DatabaseFilter() );
    $fc->addFilter( new \core\filter\RouteFilter() );
    $fc->addFilter( new \core\filter\AuthFilter() );
    $fc->addFilter( new \core\filter\ModuleEnablerFilter() );
    $fc->addFilter( new \core\filter\DispatchFilter() );
    
    $fc->execute();
} catch (SecurityException $ex) {
    // TODO: block IP? this exception only happens on hacking-like attempts
    
    include ROOT . '/modules/core/templates/exception/index.php';
} catch (DatabaseException $ex) {
    $cn = \core\Context::getInstance()->getContextName();
    if (function_exists('debug_admin_notification'))
        debug_admin_notification('Error: ' . $cn . ': ' . $ex->getMessage());
    
    include ROOT . '/modules/core/templates/exception/index.php';
} catch (\Error $ex) {
    $cn = \core\Context::getInstance()->getContextName();
    if (function_exists('debug_admin_notification'))
        debug_admin_notification('Error: ' . $cn . ': ' . $ex->getMessage());
    
    include ROOT . '/modules/core/templates/exception/index.php';
} catch (\Exception $ex) {
    $cn = \core\Context::getInstance()->getContextName();
    if (function_exists('debug_admin_notification'))
        debug_admin_notification('Error: ' . $cn . ': ' . $ex->getMessage());
    
    include ROOT . '/modules/core/templates/exception/index.php';
}

