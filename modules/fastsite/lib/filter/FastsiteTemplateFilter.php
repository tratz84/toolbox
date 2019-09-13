<?php

namespace fastsite\filter;

use fastsite\data\FastsiteSettings;
use fastsite\template\FastsiteTemplateLoader;



class FastsiteTemplateFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $uri = request_uri_no_params();

        // don't allow requests to fastsite-folder
        if (strpos($uri, '/fastsite/') === 0) {
            return $filterChain->next();
        }
        
        
        $rawtpl = get_var('rawtpl') || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'rawtpl=1') !== false);
        $authenticated = isset($_SESSION['user_id']) && $_SESSION['user_id'];
        
        if ($rawtpl && $authenticated) {
            // always try to serve
        }
        // don't service html files
        else if (strpos($uri, '.htm') !== false || strpos($uri, '.html') !== false) {
            return $filterChain->next();
        }
        
        
        $fastsiteSettings = object_container_get(FastsiteSettings::class);
        
        // get template settings
        $ts = $fastsiteSettings->getActiveTemplateSettings();
        $tf = $ts->getDefaultTemplateFile();
        
        $fth = object_container_get(FastsiteTemplateLoader::class);
        
        $tplBaseFolder = $ts->getBaseTemplateFolder();
        
        // show raw template? used @ fastsite template/templateFile's iframe
        if ($rawtpl && isset($_SESSION['user_id']) && $_SESSION['user_id']) {
            $tplBaseFolder = '';
        }
        
        // template file?
        if ($fth->serveFile( $tplBaseFolder.$uri )) {
            return;
        }
        
        
        $filterChain->next();
    }
    
}