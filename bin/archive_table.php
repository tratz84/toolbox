#!/usr/bin/env php
<?php

use core\parser\ArgumentParser;
use core\db\mysql\MysqlTableArchiver;

include dirname(__FILE__).'/../config/config.php';


if (count($argv) < 4) {
    print "Usage: {$argv[0]} <contextname> <tablename> <before-datetime> [-o <outputdir>]\n";
    exit;
}

// bootstrap
$contextName = $argv[1];
bootstrapCli($contextName);

if (valid_date($argv[3]) == false && valid_datetime($argv[3]) == false) {
    print "Invalid before-datetime\n";
    exit;
}

// parse parameters
$tableName = $argv[2];
$beforeCreated = format_date($argv[3], 'Y-m-d H:i:s');

$argumentParser = new ArgumentParser( $argv );
if ($argumentParser->hasOption('o')) {
    $outputdir = $argumentParser->getOption('o');
    
    if (is_dir($outputdir) == false) {
        print "Invalid outputdir\n";
        exit;
    }
}
else {
    $outputdir = ctx()->getDataDir() . '/archive/';
}


$mta = new MysqlTableArchiver($tableName, $beforeCreated, $outputdir);
$mta->execute();


