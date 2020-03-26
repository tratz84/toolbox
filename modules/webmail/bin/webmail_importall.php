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
use webmail\mail\ImapConnection;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;
use webmail\solr\SolrMailQuery;
use webmail\solr\SolrMailQueryResponse;
use core\parser\ArgumentParser;

if (count($argv) < 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// move to cwd
chdir(__DIR__.'/../../..');

// bootstrap
include 'config/config.php';
$contextName = $argv[1];
bootstrapCli($contextName);


ini_set('memory_limit', '2GB');

$argumentParser = new ArgumentParser( $argv );

$updateOnly = $argumentParser->hasOption('u');



// import folder
print "Importing webmail/inbox\n";
$solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
$solrImportMail->setUpdateMode( $updateOnly );
$solrImportMail->importFolder( ctx()->getDataDir().'/webmail/inbox' );
unset( $solrImportMail );
print "Importing webmail/inbox done\n\n";
die("Folder import done\n");


// loop through active Connectors to sync/fetch mail
$connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
$cs = $connectorService->readActive();

foreach($cs as $c) {
    /** @var \webmail\model\Connector $c */
    $c = $connectorService->readConnector( $c->getConnectorId() );
    
    if ($c->getConnectorType() == 'imap') {
        $ic = ImapConnection::createByConnector($c);
        if (!$ic->connect()) {
            print "Unable to connect to " . $c->getDescription() . "\n";
            continue;
        }
            
        print "Connected to " . $c->getDescription() . "\n";
        
        $strlen_dataDir = strlen(\core\Context::getInstance()->getDataDir());
        $solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
        $ic->setCallbackItemImported(function($folderName, $overview, $file) use ($solrImportMail, $strlen_dataDir) {
            
            // lookup file
            $solrMailQuery = new SolrMailQuery( WEBMAIL_SOLR );
            $id = substr($file, $strlen_dataDir);
            $solrMailQuery->addFacetSearch('id', ':', solr_escapePhrase($id));
            /** @var SolrMailQueryResponse $smqr */
            $smqr = $solrMailQuery->search();
    
            // TODO: check if .properties is changed
            if ($smqr->getNumFound() == 0) {
                $solrImportMail->queueEml( $file );
                $solrImportMail->purge( );
            }
        });
        
        
        $ic->doImport( $c );
        $ic->disconnect();
        $ic->saveMessagePropertyChecksums();
    }
    else if ($c->getConnectorType() == 'pop3') {
        
    }
    
    $solrImportMail->purge( true );
}



