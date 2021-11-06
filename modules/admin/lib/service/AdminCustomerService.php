<?php

namespace admin\service;

use core\service\ServiceBase;
use admin\model\CustomerDAO;
use core\Context;

class AdminCustomerService extends ServiceBase {
    
    
    public function readCustomers( $opts=array() ) {
        
        $ctx = $this->oc->get(Context::class);
        $user = $ctx->getUser();
        
        $customerDao = new CustomerDAO();
        
        if (is_cli() || $user->getUserType() == 'admin') {
            if (isset($opts['active-only']) && $opts['active-only'])
                return $customerDao->readActive();
            else
                return $customerDao->readAll();
        } else {
            $customerDao = new CustomerDAO();
            
            $allowedCustomers = $user->getCustomers();
            $ids = array();
            foreach($allowedCustomers as $ac) {
                $ids[] = $ac->getCustomerId();
            }
            
            return $customerDao->readCustomers($ids);
        }
    }
    
    
}