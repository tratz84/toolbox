<?php


namespace fastsite\filter;

use core\Context;
use core\exception\InvalidStateException;

class FastsiteSessionFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        // determine current contextName
        
        bootstrapContext( 'default' );
        
        $ctx = Context::getInstance();
        
        // start session for path
        $sessionPath = BASE_HREF;
        
        session_set_cookie_params(0, $sessionPath);
        
        if (get_var('c') && (strpos(get_var('c'), 'api/') === 0 || strpos(get_var('c'), 'api/') === 0)) {
            // api-calls are stateless
            
        } else {
            session_start();
        }
        
        $filterChain->next();
    }
}

