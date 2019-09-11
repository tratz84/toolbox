<?php

namespace fastsite\filter;

use fastsite\template\FastsiteTemplateLoader;



class FastsiteTemplateFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $uri = request_uri_no_params();
        
        
        $fth = object_container_get(FastsiteTemplateLoader::class);
        $fth->setTemplateName( 'startbootstrap-creative-gh-pages' );
        
        // template file?
        if ($fth->serveFile( $uri )) {
            return;
        }
        
        
        $filterChain->next();
    }
    
}