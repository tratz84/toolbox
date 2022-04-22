#!/usr/bin/env php
<?php

include dirname(__FILE__).'/../config/config.php';

chdir( dirname(__FILE__) . '/..' );


// get list of less-files
$lessFiles = array();
$lessFiles[] = 'www/css/less/base.less';

$modules = module_list();
foreach($modules as $moduleName => $path) {
    $p = $path . '/public/css/default.less';
    
    if (file_exists($p)) {
        $lessFiles[] = $p;
    }
}

// generate css
$css = '';
foreach($lessFiles as $lf) {
    $cmd = null;
    if (file_exists(__DIR__.'/../node_modules/lessc/node_modules/.bin/lessc')) {
        $cmd = realpath( __DIR__.'/../node_modules/lessc/node_modules/.bin/lessc' );
    }
    else if (file_exists(__DIR__.'/../node_modules/.bin/lessc')) {
        $cmd = realpath( __DIR__.'/../node_modules/.bin/lessc' );
    }
    
    if ($cmd == null) {
        print "Error: lessc not found\n";
        exit;   
    }
    
    $cmd = $cmd . ' -x ' . $lf;

    print "Executing command: {$cmd}\n";
    $result_code = null;
    $output = null;
    exec( $cmd, $output, $result_code );
    
    // check result code
    if ($result_code !== 0) {
        throw new \core\exception\InvalidStateException( 'Invalid result code lessc: '.$result_code );
    }
    
    $css .= $output[0];
    $css .= "\n\n";
}

file_put_contents('www/css/less/style.css', $css);



