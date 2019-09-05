<?php

namespace fastsite\filter;

use core\Context;
use fastsite\FastsiteTemplateHelper;


class FastsiteTemplateFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $uri = request_uri_no_params();
        
        $fth = object_container_get(FastsiteTemplateHelper::class);
        $fth->setTemplateName( 'business-casual-gh-pages' );
        
        // template file?
        if ($fth->serveFile( $uri )) {
            return;
        }
        
        
        $filterChain->next();
    }
    
}