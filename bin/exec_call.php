#!/usr/bin/env php
<?php

/**
 * bin/exec_call.php executed a Php-call
 * 
 */

include dirname(__FILE__).'/../config/config.php';

if (is_debug() == false) {
    print "Exiting.. debug only at the moment\n";
    exit;
}


\core\Context::getInstance()->enableModule('admin');

$params = array();
// standalone?
if (is_standalone_installation()) {
    if (count($argv) < 2) {
        print "Usage: {$argv[0]} <context> <call> <params..>\n";
        exit;
    }
    
    bootstrapCli( 'default' );
    
    $params = array_splice($argv, 1);
}
// multi-installation? => loop all through customers
else {
    if (count($argv) < 3) {
        print "Usage: {$argv[0]} <context> <call> <params..>\n";
        exit;
    }
    
    bootstrapCli( $argv[1] );
    
    $params = array_splice($argv, 2);
}


$call = $params[0];
$funcParams = array_splice( $params, 1 );
$strParams = count($funcParams) > 0 ? "'".implode("', '", $funcParams)."'" : "";


if (strpos($call, '::') !== false) {
    list($className, $call) = explode('::', $call);
    
    if (class_exists( $className ) == false) {
        print 'Class not found: ' . $className . "\n";
        exit;
    }
    
    // lookup class
    $ref = new ReflectionClass( $className );
    
    if ($ref->hasMethod( $call ) == false) {
        print 'Method not found: ' . $call . "\n";
        exit;
    }
    
    $m = $ref->getMethod( $call );
    
    // call method
    print "Calling {$className}::{$call}({$strParams}) ...\n";
    if ($m->isStatic()) {
        $r = $m->invokeArgs( null, $funcParams );
    }
    else {
        $obj = object_container_get( $className );
        
        $r = call_user_func_array(array($obj, $call), $funcParams);
    }
    
    // output result
    print "Result: " . var_export($r, true) . "\n";
}
else {
    if (function_exists( $call ) == false) {
        print "Function not found: " . $call . "\n";
        exit;
    }
    
    print "Calling $call({$strParams})\n";
    $r = call_user_func_array($call, $funcParams);
    
    // output result
    print "Result: " . var_export($r, true) . "\n";
}



