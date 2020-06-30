#!/usr/bin/env php
<?php

use core\db\DatabaseHandler;
use core\filter\FilterChain;

include dirname(__FILE__).'/../config/config.php';


if (count($argv) < 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// bootstrap
$contextName = $argv[1];
bootstrapContext($contextName);

// init DatabaseHandler
$fc = new FilterChain();
$fc->addFilter( new \core\filter\DatabaseFilter() );
$fc->execute();



module_update_handler('base', 'init', ['init' => true]);

$sqldata = file_get_contents(__DIR__.'/../doc/sql/basedata.sql');

$mysqlcon = DatabaseHandler::getConnection('default');
$mysqli = $mysqlcon->getResource();

if ($mysqli->multi_query($sqldata)) {
    do {
        // ...
        print "Next query..\n";
    } while ($mysqli->next_result());
}




exit;
