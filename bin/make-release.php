#!/usr/bin/env php
<?php

include dirname(__FILE__).'/../config/config.php';

if (is_standalone_installation() && count($argv) != 2) {
    print "Config is in standalone mode\n";
    print "Usage: {$argv[0]} <target-dir>\n";
    exit;
}
if (is_standalone_installation() == false && count($argv) != 3) {
    print "Config is NOT in standalone mode\n";
    print "Usage: {$argv[0]} <context> <target-dir>\n";
    exit;
}

bootstrapContext( is_standalone_installation() ? 'default' : $argv[1] );

$targetDir = is_standalone_installation() ? $argv[1] : $argv[2];
$targetDir = realpath($targetDir);

if ($targetDir == false || is_dir($targetDir) == false) {
    die('Invalid target dir');
}

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
    if ($ctx->isModuleEnabled( $module_name )) {
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
