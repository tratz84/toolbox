<?php

namespace admin\service;


use admin\model\CustomerDAO;
use core\service\ServiceBase;

class ContextService extends ServiceBase {
    
    
    /**
     * 
     * @param string $contextName
     * @return NULL|\admin\model\Customer
     */
    public function readCustomerContext($contextName) {
        $cDao = new CustomerDAO();
        
        return $cDao->readByName($contextName);
    }
    
    public function updateContextDescription( $contextName, $description ) {
        
        $cDao = new CustomerDAO();
        
        $cDao->updateDescriptionByName( $contextName, $description );
        
    }
    
}
