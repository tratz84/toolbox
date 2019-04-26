<?php


use core\Context;
use core\exception\InvalidStateException;
use core\ObjectContainer;
use core\template\HtmlScriptLoader;

require_once dirname( __FILE__ ) . '/functions/bootstrap.php';
require_once dirname( __FILE__ ) . '/functions/misc.php';
require_once dirname( __FILE__ ) . '/functions/networking.php';
require_once dirname( __FILE__ ) . '/functions/html.php';
require_once dirname( __FILE__ ) . '/functions/currency.php';
require_once dirname( __FILE__ ) . '/functions/solr.php';
require_once dirname( __FILE__ ) . '/functions/lang.php';
require_once dirname( __FILE__ ) . '/functions/db.php';
require_once dirname( __FILE__ ) . '/functions/auth.php';
require_once dirname( __FILE__ ) . '/functions/forms.php';
require_once dirname( __FILE__ ) . '/functions/hook-helper.php';


register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && isset($err['type']) && $err['type'] == E_ERROR) {
        debug_admin_notification('Error: ' . $err['message'] . ', ' . $err['file'].':'.$err['line']);
    }
});
    


spl_autoload_register(function($name) {
    if (strpos($name, 'context\\') === 0) {
        $ctx = Context::getInstance();
        
        $name = substr($name, strlen('context\\'));
        $classContextName = substr($name, 0, strpos($name, '\\'));
        $name = str_replace('\\', '/', $name);
        $name = substr($name, strlen($classContextName.'\\'));
        
        $f = ROOT . '/context/'.$classContextName.'/lib/'.$name.'.php';
        $f = realpath( $f );

        if ($f && strpos($f, realpath(ROOT . '/context/')) === 0) {
            include $f;
            return;
        }
    }
    
    
    
    
    // load by module
    $modules = list_files( ROOT . '/modules' );
    
    $classPath = str_replace('\\', '/', $name);
    
    $moduleClassName = null;
    
    // strip module-name @ classname
    foreach($modules as $m) {
        if (strpos($classPath, $m.'/') === 0) {
            $classPath = substr($classPath, strlen($m)+1);
            $moduleClassName = $m;
        }
    }
    
    
    if ($moduleClassName === null) {
        return;
//         throw new InvalidStateException('Module not found for classname: ' . $name);
    }
    
    $path = realpath(ROOT . '/modules/' . $moduleClassName);
    
    
    if (is_dir( $path )) {
        // lib-files
        $file = realpath( $path . '/lib/' . $classPath . '.php' );
        
        if ($file === false) {
            throw new InvalidStateException('File for class not found, class: ' . $name . ', file: ' . ($path . '/lib/' . $classPath . '.php'));
        }
        
        if ($file && is_file($file)) {
            // file must be inside lib-dir
            if (strpos($file, $path) === 0) {
                require_once $file;
            }
        }
    }
    
});

