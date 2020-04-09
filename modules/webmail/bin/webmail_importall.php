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
    $solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
    $solrImportMail->setUpdateMode( $updateOnly );
    $solrImportMail->importFolder( ctx()->getDataDir().'/webmail/inbox' );
    unset( $solrImportMail );
    print "DONE Folder import\n";
}


if ($argumentParser->hasOption('skip-connector-import') == false) {
    print "START Connector import\n";
    
    // loop through active Connectors to sync/fetch mail
    /** @var ConnectorService $connectorService */
    $connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);
    $cs = $connectorService->readActive();
    
    foreach($cs as $c) {
        /** @var \webmail\model\Connector $c */
        $c = $connectorService->readConnector( $c->getConnectorId() );
        
        
        // save last update time for incremental updates
        $start_time_run = time();
        
        if ($c->getConnectorType() == 'imap') {
            $ic = ImapConnection::createByConnector($c);
            if (!$ic->connect()) {
                print "Unable to connect to " . $c->getDescription() . "\n";
                continue;
            }
                
            print "Connected to " . $c->getDescription() . "\n";
            
            // update only? just update messages from yesterday & today
            if ( $updateOnly ) {
                $ic->setSinceUpdate( date('Y-m-d', strtotime('-1 day')) );
            }
            
            
            $sentImapFolder =  $connectorService->readImapFolder( $c->getSentConnectorImapfolderId() );
            
            
            $solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
            $ic->setCallbackItemImported(function($folderName, $overview, $file, $changed) use ($solrImportMail, $sentImapFolder) {
                // this callback is only called on new mail and changed (IMAP-properties like isRead & folderName can change)
//                 print "Queueing file: $file\n";
                if ($changed) {
                    $solrImportMail->queueEml( $file );
                    $solrImportMail->purge( );
                    
                    // new mail in Sent-folder? => mark In-Reply-To-mail as REPLIED if status is OPEN
                    if ($sentImapFolder && $sentImapFolder->getFolderName() == $folderName) {
                        $sma = new SolrMailActions();
                        $sma->autoMarkMessageAsReplied( $solrImportMail->getLastInReplyTo() );
                    }
                }
            });
            
            $ic->doImport( $c );
            $ic->disconnect();
            $ic->saveServerPropertyChecksums();
            
            $solrImportMail->purge( true );
        }
        else if ($c->getConnectorType() == 'pop3') {
            
        }
        
        // save time when run for Connector started, for incremental updates
        object_meta_save(Connector::class, $c->getConnectorId(), 'webmail_importall-lastrun', $start_time_run);
    }
    
    print "DONE Connector import\n";
}



