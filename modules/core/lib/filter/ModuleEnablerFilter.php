<?php

namespace core\filter;

use core\Context;
use core\module\ModuleMeta;
use core\module\ModuleLoader;


class ModuleEnablerFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $ctx = Context::getInstance();
        
        
        // must have modules
        include ROOT . '/modules/core/autoload.php';
        include ROOT . '/modules/base/autoload.php';
        
        $modulesToLoad = array();
        
        // load dynamic modules, old stuff must be rewritten to load this way..
        $modules = module_list();
        foreach($modules as $moduleName => $path) {
            if (file_exists($path . '/meta.php') == false)
                continue;
            if (file_exists($path . '/autoload.php') == false)
                continue;
            
            // load meta-info
            $meta = load_php_file( $path . '/meta.php' );
            
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
                $modulesToLoad[] = array('meta' => $meta, 'autoload' => $path.'/autoload.php');
            }
        }

        // sort by prio
        usort($modulesToLoad, function($o1, $o2) {
            return $o1['meta'][0]->getPrio() - $o2['meta'][0]->getPrio();
        });
        
        // load autoload.php for modules
        foreach($modulesToLoad as $m) {
            $ml = new ModuleLoader($m['meta'], $m['autoload']);
            $ml->load();
        }
        
        
        
        $filterChain->next();
    }
    
}

