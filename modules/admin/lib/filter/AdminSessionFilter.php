<?php


namespace admin\filter;


class AdminSessionFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        
        // start session for path
        session_set_cookie_params(0, '/admin/');
        session_start();
        
        $filterChain->next();
    }
    
}