<?php


spl_autoload_register(function($name) {
    if (strpos($name, 'Horde_') !== 0) {
        return;
    }
    
    $path = str_replace('_', '/', $name).'.php';
    
    $path = realpath( __DIR__.'/horde/lib/'.$path );
    
    if ($path && is_file($path) && strpos($path, realpath( __DIR__.'/horde/lib/')) === 0) {
        require_once $path;
    }
});
