#!/usr/bin/env php
<?php

use core\db\DatabaseHandler;
use core\filter\FilterChain;

include dirname(__FILE__).'/../config/config.php';

if (is_standalone_installation() == true) {
    die("Error: Standalone installation, unable to create new environment\n");
}

if (count($argv) != 3) {
    print "Usage: {$argv[0]} <contextname> <admin-password>\n";
    exit;
}

$contextName = $argv[1];
$adminPassword = $argv[2];


// check if database exists
$dbcount = DatabaseHandler::getConnection('admin')->queryValue("select count(*) from information_schema.schemata where schema_name=?", array('toolbox_'.$contextName));
if ($dbcount == 0) {
    die("Error: Database not yet created\n");
}

// insert customer
DatabaseHandler::getConnection('admin')->query('insert into insights__customer set contextName=?, databaseName=?, active=1, experimental=0', array($contextName, 'toolbox_'.$contextName));


// bootstrap
bootstrapContext($contextName);

// init DatabaseHandler
$fc = new FilterChain();
$fc->addFilter( new \core\filter\DatabaseFilter() );
$fc->execute();



module_update_handler('base', 'init', ['init' => true]);

DatabaseHandler::getConnection('default')->query( "insert into base__user set username='admin', password=?, edited=now(), created=now(), user_type='admin'", array($adminPassword) );

