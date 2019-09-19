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
        
        // let other plugins be able to hook on routing
        hook_eventbus_publish($this, 'fastsite', 'FastsiteRouteFilter::doFilter');
        
        $filterChain->next();
    }
    
    
}
