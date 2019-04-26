<?php

use \core\Context;

define('ADMIN_CONTEXT', true);

include '../config/config.php';

if (is_standalone_installation()) {
    die('Admin not available in standalone mode');
}

try {
    Context::getInstance()->setEnabledModules(array('core', 'admin'));
    
    $fc = new \core\filter\FilterChain();
    
    $fc->addFilter( new \admin\filter\AdminSessionFilter() );
    $fc->addFilter( new \admin\filter\AdminRouteFilter() );
    $fc->addFilter( new \admin\filter\AdminAuthFilter() );
    $fc->addFilter( new \admin\filter\AdminDispatchFilter() );
    
    $fc->execute();
    
} catch (\Exception $ex) {
    include ROOT . '/modules/core/templates/exception/index.php';
}

