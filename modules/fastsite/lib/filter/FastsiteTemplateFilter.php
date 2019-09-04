<?php

namespace fastsite\filter;

use core\Context;


class FastsiteTemplateFilter {
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
        
        print 'jo';
        
        $filterChain->next();
    }
    
}