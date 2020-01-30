<?php


namespace core\filter;

use core\Context;
use core\exception\ContextNotFoundException;

class SessionFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        // determine current contextName
        
        if (is_standalone_installation()) {
            $contextName = 'default';
        } else {
            $uri = '/'.     substr($_SERVER['REQUEST_URI'], strlen(BASE_HREF));
            
            $matches = array();
            if (preg_match('/^\\/([a-zA-Z0-9_-]+)?\\/.*/', $uri, $matches) == false) {
                throw new ContextNotFoundException('context not found');
            }
            $contextName = $matches[1];
        }
        
        bootstrapContext( $contextName );
        
        $ctx = Context::getInstance();
        
        // start session for path
        if (is_standalone_installation()) {
            $sessionPath = BASE_HREF;
        } else {
            $sessionPath = BASE_HREF.$ctx->getContextName().'/';
        }
        session_set_cookie_params(0, $sessionPath);
        
        if (get_var('c') && (strpos(get_var('c'), 'api/') === 0 || strpos(get_var('c'), 'api/') === 0)) {
            // api-calls are stateless
            
        } else {
            session_start();
        }
        
        $filterChain->next();
    }
    
}