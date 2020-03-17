#!/usr/bin/php
<?php

/**
 * webmail_importall.php
 * 
 * !!! NOTE if this script Segfaults, update Php's mailparse.so extension !!!
 */


use core\ObjectContainer;
use webmail\mail\ImapConnection;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// move to cwd
chdir(dirname(__FILE__));

// bootstrap
include '../config/config.php';
$contextName = $argv[1];
bootstrapCli($contextName);


ini_set('memory_limit', '2GB');


$connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);

$cs = $connectorService->readActive();

foreach($cs as $c) {
    $c = $connectorService->readConnector( $c->getConnectorId() );
    
    $ic = ImapConnection::createByConnector($c);
    
    if (!$ic->connect()) {
        print "Unable to connect to " . $c->getDescription() . "\n";
        continue;
    }
        
    print "Connected to " . $c->getDescription() . "\n";
    
    
    $solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
    $ic->setCallbackItemImported(function($folderName, $overview, $file) use ($solrImportMail) {
        // TODO: check if email is actually changed
        $solrImportMail->queueEml( $file );
        $solrImportMail->purge( );
    });
    
    
    $ic->doImport( $c );
    $ic->disconnect();
    $ic->saveMessagePropertyChecksums();
    
    $solrImportMail->purge( true );
}
