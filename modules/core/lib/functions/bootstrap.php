<?php


use core\Context;
use core\exception\InvalidStateException;
use admin\model\Customer;

function bootstrapContext($contextName) {
    
    if (is_standalone_installation()) {
        $customer = new Customer();
        $customer->setContextName('default');
        $customer->setDatabaseName(DEFAULT_DATABASE_NAME);
        $customer->setActive(true);
    } else {
        $contextService = new \admin\service\ContextService();
        $customer = $contextService->readCustomerContext( $contextName );
    }
    
    if ($customer == null)
        throw new InvalidStateException('customer not found');
        
    $ctx = Context::getInstance();
    $ctx->setCustomer($customer);
    $ctx->setContextName($contextName);
    
    $autoloadfile = ROOT . '/context/'.$contextName.'/autoload.php';
    
    if (file_exists($autoloadfile)) {
        load_php_file($autoloadfile);
    }
    
}

/**
 * is_standalone_installation() - single installation with only one default context?
 * 
 * @return boolean
 */
function is_standalone_installation() {
    if (defined('STANDALONE_INSTALLATION') && STANDALONE_INSTALLATION) {
        return true;
    } else {
        return false;
    }
}


