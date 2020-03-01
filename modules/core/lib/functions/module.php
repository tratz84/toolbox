<?php



use core\Context;
use base\service\SettingsService;

function module_list($forceReload=false) {
    static $modules = null;
    
    if ($forceReload || $modules === null) {
        $modules = array();
        
        $moduleDirs = Context::getInstance()->getModuleDirs();
        foreach($moduleDirs as $md) {
            $moduleFiles = list_files($md);
            
            if ($moduleFiles === false) {
                trigger_error('Invalid module directory: '.$md, E_USER_NOTICE);
                continue;
            }
            
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

function module_exists($moduleName) {
    $ml = module_list();
    
    return isset($ml[$moduleName]) ? true : false;
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

function module_file_safe($module, $path, $subpath) {
    $p1 = module_file($module, $path);
    if (!$p1)
        return false;
    
    $p2 = module_file($module, $path . '/' . $subpath);
    if (!$p2)
        return false;
    
    if (strpos($p2, $p1) !== 0)
        return false;
    
    return $p2;
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
            $l[] = 'module/'.$moduleName.'/css/default.less';
        }
    }
    
    return $l;
}


/**
 * module_update_handler()
 * - Loads 'modules/<module name>/update.php' if $version is not 
 *   the current (both DOWN and UPgrades!). Good place to call 
 *   this function is in the autoload.php
 */
function module_update_handler($moduleName, $version) {
    lock_system('module-update');
    
    $settingsKey = 'module-'.$moduleName.'-version';
    
    $ctx = \core\Context::getInstance();
    $curVer = $ctx->getSetting( $settingsKey );
    
    // note, this way update.php get both called on downgrades & upgrades. Script should handle it right!
    if ($curVer != $version) {
        $updatefile = module_file($moduleName, '/update.php');
        
        // check if file is found & include
        if ($updatefile) {
            load_php_file( $updatefile );
        }
        
        // update version
        $settingsService = object_container_get(SettingsService::class);
        $settingsService->updateValue($settingsKey, $version);
    }
    
    // note: update might throw an error and this point isn't reached, so
    //       the lock isn't released. This is design on purpose, if an
    //       update failes manual action is required
    release_system_lock('module-update');
}

