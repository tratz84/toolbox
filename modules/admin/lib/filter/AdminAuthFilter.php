<?php

namespace admin\filter;

use core\Context;
use admin\service\AdminUserService;
use core\ObjectContainer;


class AdminAuthFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
        
        if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] == true) {
            $oc = ObjectContainer::getInstance();
            
            $adminUserService = $oc->get(AdminUserService::class);
            $user = $adminUserService->readUser($_SESSION['user_id']);
            
            $ctx->setUser($user);
        } else {
            $ctx->setModule('admin');
            $ctx->setController('auth');
            $ctx->setAction('index');
        }
        
        
        $filterChain->next();
    }
    
}

