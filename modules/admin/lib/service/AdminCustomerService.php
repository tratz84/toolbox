<?php

namespace admin\service;

use core\service\ServiceBase;
use admin\model\CustomerDAO;
use core\Context;

class AdminCustomerService extends ServiceBase {
    
    
    public function readCustomers() {
        
        $ctx = $this->oc->get(Context::class);
        $user = $ctx->getUser();
        
        $customerDao = new CustomerDAO();
        
        if (is_cli() || $user->getUserType() == 'admin') {
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