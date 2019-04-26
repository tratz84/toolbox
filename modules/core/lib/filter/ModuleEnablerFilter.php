<?php

namespace core\filter;

use core\Context;
use core\module\ModuleMeta;


class ModuleEnablerFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
        
        
        // must have modules
        include ROOT . '/modules/core/autoload.php';
        include ROOT . '/modules/base/autoload.php';
        
        
        // load dynamic modules, old stuff must be rewritten to load this way..
        $moduleNames = list_files(ROOT . '/modules/');
        foreach($moduleNames as $moduleName) {
            if (file_exists(ROOT . '/modules/' . $moduleName . '/meta.php') == false)
                continue;
            if (file_exists(ROOT . '/modules/' . $moduleName . '/autoload.php') == false)
                continue;
            
            // load meta-info
            $meta = load_php_file( ROOT . '/modules/' . $moduleName . '/meta.php' );
            
            // invalid response? => skip
            if (is_a($meta, ModuleMeta::class) == false && is_array($meta) == false)
                continue;
            
            if (is_array($meta) == false) { $meta = array($meta); }
            
            // check of a ModuleMeta-instance is enabled
            $moduleEnabled = false;
            foreach($meta as $m2) {
                if ($ctx->getSetting($m2->getTag().'Enabled')) {
                    $moduleEnabled = true;
                    break;
                }
            }
            
            // module enabled? => include autoload.php
            if ($moduleEnabled) {
                $autoloadfile = ROOT . '/modules/'.$moduleName.'/autoload.php';
                load_php_file( $autoloadfile );
            }
        }
        
        
        
        $filterChain->next();
    }
    
}

