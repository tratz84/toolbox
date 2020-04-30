#!/usr/bin/php
<?php

/**
 * webmail_importall.php
 * 
 * !!! NOTE if this script Segfaults, update Php's mailparse.so extension !!!
 * 
 * script imports from 2 sources
 * - active 'connectors': IMAP / POP3 / (..)
 * - data/webmail/inbox-folder
 * 
 */


use core\ObjectContainer;
use core\parser\ArgumentParser;
use webmail\mail\ImapConnection;
use webmail\mail\SolrMailActions;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;

if (count($argv) < 2) {
    print "Usage: {$argv[0]} <contextname> [-u] [--skip-folder-import] [--skip-connector-import]\n";
    exit;
}

// move to cwd
chdir(__DIR__.'/../../..');

// bootstrap
include 'config/config.php';
$contextName = $argv[1];
bootstrapCli($contextName);


ini_set('memory_limit', '2GB');
// set_time_limit(60 * 5);              // DONT set a timelimit, script might run longer and MUST finish, because it creates a checksum-file for updates


$argumentParser = new ArgumentParser( $argv );

$updateOnly = $argumentParser->hasOption('u');


// import folder
if ($argumentParser->hasOption('skip-folder-import') == false) {
    print "START Folder import, importing webmail/inbox\n";
    webmail_import_folder( $updateOnly );
    print "DONE Folder import\n";
}


if ($argumentParser->hasOption('skip-connector-import') == false) {
    print "START Connector import\n";
    webmail_import_connectors($updateOnly);
    print "DONE Connector import\n";
}



