<?php

namespace core\filter;



class UrlSecurityFilter {
    
    public function __construct() {
        
    }
    
    public function doFilter($filterChain) {
        $urlstoban = array();
        
        $urlstoban['/wp-login.php']        = true;
        $urlstoban['/wp-admin/']           = true;
        $urlstoban['/wp-addmin/index.php'] = true;
        $urlstoban['/robots.txt']          = true;
        
        if (isset($urlstoban[ request_uri_no_params() ])) {
            // these URL's should NEVER be requested.. ban IP ?
            
            header("HTTP/1.0 404 Not Found");
            header("Refresh: 5; URL=" . BASE_URL);
            die('404 - Url not found');
        }
        
        // TODO: check banning mechanism
        
        // TODO: algo for checking exceptions. More then .. exceptions in 1 minute? => ban?
        
        $filterChain->next();
    }
    
}

