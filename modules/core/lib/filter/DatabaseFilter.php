<?php


namespace core\filter;


use core\Context;
use core\db\DatabaseHandler;
use core\db\DatabaseUpdater;
use core\exception\InvalidStateException;

class DatabaseFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
    
        $customer = $ctx->getCustomer();
        
        // connect to database for current context
        $dh = \core\db\DatabaseHandler::getInstance();
        $dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, $customer->getDatabaseName());
        
        
        if (defined('SQL_VERSION')) {
            if (preg_match('/^\\d{10}$/', SQL_VERSION) == false)
                throw new InvalidStateException('Invalid SQL_VERSION set');
            
            $sqlVersion = (int)$ctx->getSetting('SQL_VERSION', 0);
            if ($sqlVersion < SQL_VERSION) {
                // exec update
                $db = new DatabaseUpdater('SQL_VERSION', SQL_VERSION, ROOT.'/updates');
                $db->update();
            }
        }
        
        
        $filterChain->next();
        
        DatabaseHandler::getInstance()->closeAll();
    }
    
}