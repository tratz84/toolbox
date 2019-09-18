<?php

namespace fastsite\filter;

use fastsite\data\FastsiteSettings;
use fastsite\template\FastsiteTemplateLoader;



class FastsiteMediaFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $uri = request_uri_no_params();
        
        // don't allow requests to fastsite-folder
        if (strpos($uri, '/fastsite/') === 0) {
            return $filterChain->next();
        }
        
        $uri = substr($uri, strlen(BASE_HREF));
        if (strpos($uri, 'fs-media/') === 0) {
            
            $fsMediaPath = substr($uri, strlen('fs-media/'));
            
            $f = get_data_file_safe('fastsite/fs-media/', $fsMediaPath);
            
            if (!$f) {
                die('File not found');
            }
            
//             header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24))); // 24 hours
//             header("Pragma: cache");
//             header("Cache-Control: max-age=3600");
            header("Keep-Alive: timeout=5, max=100");
            header('Content-type: ' . file_mime_type($f));
            
            readfile( $f );
        }
        
        
        
        $filterChain->next();
    }
    
}