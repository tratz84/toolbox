#!/usr/bin/env php
<?php

include dirname(__FILE__).'/../config/config.php';

// validate arguments
if (count($argv) != 3) {
    print "Usage: {$argv[0]} <context|'default'> <target-dir>\n";
    exit;
}

$contextName = $argv[1];
if (is_standalone_installation() && $contextName != 'default') {
    die('Invalid contextname given. In standalone-mode context must be "default"');
}


$targetDir = $argv[2];
$targetDir = realpath($targetDir);
if ($targetDir == false || is_dir($targetDir) == false) {
    die('Invalid target dir');
}


// bootstrap
bootstrapContext( $contextName );

// connect to db
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, \core\Context::getInstance()->getCustomer()->getDatabaseName());

// enable modules
$ctx = \core\Context::getInstance();
$mef = new \core\filter\ModuleEnablerFilter();
$mef->enableModules();


// copy base files
$rootfiles = list_files(ROOT);
$files = array();
foreach($rootfiles as $rf) {
    if (strpos($rf, '.') === 0) continue;
    if (in_array($rf, ['modules', 'node_modules', 'package-lock.json'])) continue;
    
    if (is_dir(ROOT . '/' . $rf)) {
        $tmpsubfiles = list_files(ROOT . '/' . $rf, ['recursive' => true]);
        $subfiles = array();
        foreach($tmpsubfiles as $sf) {
            if ($rf == 'config' && in_array($sf, ['config-local.php'])) continue;
            
            $subfiles[] = realpath( ROOT . '/' . $rf . '/' . $sf );
        }
        $files = array_merge($files, $subfiles);
    } else {
        $files[] = realpath( ROOT . '/' . $rf );
    }
}
print "Copying base files\n";
copy_release_files($files, $targetDir);



// copy modules
$modules = module_list();
foreach($modules as $module_name => $path) {
    if ($ctx->isModuleEnabled( $module_name ) || in_array($module_name, ['admin'])) {
        $path = realpath($path);
        
        print "Copying module: $module_name\n";
        $modulefiles = list_files($path, ['recursive' => true]);
        for($x=0; $x < count($modulefiles); $x++) {
            $modulefiles[$x] = realpath($path . '/' . $modulefiles[$x]);
        }
        
        $basepath = substr($path, 0, strlen($path)-strlen('/modules/'.$module_name));
        
        copy_release_files($modulefiles, $targetDir, $basepath);
    }
}






function copy_release_files($files, $targetDir, $basedir=ROOT) {
    foreach($files as $f) {
        $subpath = substr($f, strlen($basedir)+1);
        //     print $f . ' => ' . $targetDir . '/' . $subpath . PHP_EOL;
        $target = $targetDir . '/' . $subpath;
        
        $dir = dirname($target);
        
        if (is_dir($dir) == false) {
            mkdir($dir, 0755, true);
        }
        
        if (is_file($f)) {
            // skip identical files
            if (file_exists($target) && md5_file($f) == md5_file($target)) continue;
            
            if (!copy($f, $target)) {
                die('Copy failed for file: ' . $f);
            }
        }
    }
}
