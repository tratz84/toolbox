<?php



use core\Context;

function module_list($forceReload=false) {
    static $modules = null;
    
    if ($forceReload || $modules === null) {
        $modules = array();
        
        $moduleDirs = Context::getInstance()->getModuleDirs();
        foreach($moduleDirs as $md) {
            $moduleFiles = list_files($md);
            
            foreach($moduleFiles as $mf) {
                $path = realpath( $md . '/' . $mf );
                
                if ($path && is_dir($path)) {
                    $modules[$mf] = $path;
                }
            }
        }
    }
    
    return $modules;
}


function module_file($module, $path) {
    $modules = module_list();
    
    
    if (isset($modules[$module]) == false)
        return false;
    
    $p = realpath( $modules[$module] . '/' . $path );
    
    if ($p && strpos($p, $modules[$module]) !== false) {
        return $p;
    }
    
    return false;
}


function module_file2module($path) {
    $modules = module_list();
    
    foreach( $modules as $moduleName => $modulePath) {
        if (strpos($path, $modulePath) !== false)
            return $moduleName;
    }
    
    return false;
}

function module_path($moduleName) {
    $modules = module_list();
    
    if (isset($modules[$moduleName]))
        return $modules[$moduleName];
    
    return false;
}

