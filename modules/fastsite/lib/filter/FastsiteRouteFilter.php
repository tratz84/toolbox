<?php


namespace fastsite\filter;

use core\Context;

class FastsiteRouteFilter {
    
    
    protected $fixedRoutes = array();
    
    
    public function __construct() {
        
    }
    
    public function doFilter($filterChain) {
        
        $ctx = Context::getInstance();
        
        $ctx->setModule( 'fastsite' );
        $ctx->setController( 'public/webpage' );
        $ctx->setAction( 'index' );
        
        $filterChain->next();
    }
    
    
}
