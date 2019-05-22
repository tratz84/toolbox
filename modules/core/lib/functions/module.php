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

/**
 * public_module_file_by_url() - returns public-module-file by given url
 */
function public_module_file_by_url($uri) {
    $moduleName = null;
    $modulePath = null;
    
    if (strpos($uri, '/module') === 0)
        $uri = substr($uri, 1);
    
    if (strpos($uri, 'module') !== 0)
        return false;
    
    $uri = substr($uri, strlen('module/'));
    
    $moduleName = substr($uri, 0, strpos($uri, '/'));
    $modulePath = substr($uri, strlen($moduleName)+1);
    
    if (!$moduleName || !$modulePath)
        return false;
    
    return module_file($moduleName, '/public/'.$modulePath);
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


/**
 * module_less_defaults() - returns list of module-specific default.less
 */
function module_less_defaults() {
    $modules = module_list();
    $l = array();
    
    foreach($modules as $moduleName => $path) {
        if (file_exists($path . '/public/css/default.less')) {
            $l[] = '/module/'.$moduleName.'/css/default.less';
        }
    }
    
    return $l;
}


