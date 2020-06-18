<?php

namespace core\filter;

use core\Context;
use core\module\ModuleMeta;
use core\module\ModuleLoader;
use core\exception\InvalidStateException;


class ModuleEnablerFilter {
    
    
    public function __construct() {
        
    }
    
    
    public function doFilter($filterChain) {
        $this->enableModules();
        
        $filterChain->next();
    }
    
    public function enableModules() {
        $ctx = Context::getInstance();
        
        
        // must have modules
        include ROOT . '/modules/core/autoload.php';
        
        $modulesToLoad = array();
        
        // load dynamic modules, old stuff must be rewritten to load this way..
        $modules = module_list();
        
        
        $moduleMetas = array();
        
        foreach($modules as $moduleName => $path) {
            if (file_exists($path . '/meta.php') == false)
                continue;
            if (file_exists($path . '/autoload.php') == false)
                continue;
            
            // already loaded? => skip (base module..)
            if (ctx()->isModuleEnabled($moduleName) && $moduleName != 'base') {
                continue;
            }
            
            // load meta-info
            $meta = load_php_file( $path . '/meta.php' );
            
            // invalid response? => skip
            if (is_a($meta, ModuleMeta::class) == false && is_array($meta) == false)
                continue;
            
            // set moduleMetas
            if (is_array($meta)) {
                foreach($meta as $m) {
                    $m->setProperty('path', $path);
                    $moduleMetas[ $m->getTag() ] = $m;
                }
            } else {
                $meta->setProperty('path', $path);
                $moduleMetas[ $meta->getTag() ] = $meta;
            }
        }
        
        $loadedModules = array();
        foreach($moduleMetas as $meta) {
            // check of a ModuleMeta-instance is enabled
            $moduleEnabled = false;
            
            if ($ctx->getSetting($meta->getTag().'Enabled')) {
                $moduleEnabled = true;
            }
            
            // mandatory modules
            if (in_array($moduleName, ['base'])) {
                $moduleEnabled = true;
            }
            
            // module enabled? => include autoload.php
            if ($moduleEnabled) {
                $modulesToLoad[] = array('meta' => $meta, 'autoload' => $meta->getProperty('path').'/autoload.php');
                
                $loadedModules[ $meta->getTag() ] = true;
                
                foreach( $meta->getDependencies() as $tag ) {
                    if (isset($loadedModules[ $tag ]) == false) {
                        if (isset($moduleMetas[ $tag ]) == false) {
                            throw new InvalidStateException('Sub module not found: '.var_export($tag, true));
                        }
                        
                        $loadedModules[ $tag ] = true;
                        $depmod = $moduleMetas[ $tag ];
                        
                        $modulesToLoad[] = array('meta' => $depmod, 'autoload' => $depmod->getProperty('path').'/autoload.php');
                    }
                }
            }
        }

        // sort by prio
        usort($modulesToLoad, function($o1, $o2) {
            return $o1['meta']->getPrio() - $o2['meta']->getPrio();
        });
        
        // load autoload.php for modules
        foreach($modulesToLoad as $m) {
            $ml = new ModuleLoader($m['meta'], $m['autoload']);
            $ml->load();
        }
        
    }
    
}

