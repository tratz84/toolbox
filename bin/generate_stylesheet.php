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
    if (is_windows()) {
        $cmd = 'node_modules\.bin\lessc -x '.$lf;
    } else {
        $cmd = './node_modules/.bin/lessc -x '.$lf;
    }

    print "Executing command: {$cmd}\n";
    $css .= `$cmd`;
    $css .= "\n\n";
}

file_put_contents('www/css/less/style.css', $css);



