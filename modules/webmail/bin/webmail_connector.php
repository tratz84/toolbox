#!/usr/bin/php
<?php

/**
 * modules/webmail/bin/webmail_connector.php - monitors mailboxes for given contextname
 * 
 */


use core\ObjectContainer;
use core\db\DatabaseHandler;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;
use webmail\mail\connector\ImapConnector;

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// bootstrap
chdir(__DIR__.'/../../../');
include 'config/config.php';

$contextName = $argv[1];
bootstrapCli($contextName);


/** @var ConnectorService $connectorService */
$connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);

$monitors = array();

$cnt=0;
while (true) {

    // check if connectors are changed?
    if ($cnt == 0) {
        // check database connection
        $con = DatabaseHandler::getConnection('default');
        if ($con->ping() == false) {
            print_info('Exit... MySQL ping failed');
            exit;
        }
        
        
        $connectors = $connectorService->readActive();

        if (count($connectors) == 0) {
            print_info("No active connectors");
        }
        
        // check if connectors are changed
        $connectorIds = array();
        /** @var Connector $c */
        foreach($connectors as $c) {
            $connectorId = $c->getConnectorId();
            $connectorIds[] = $connectorId;
            
            // monitor changed? => stop current
            if (isset($monitors[$connectorId])) {
                $connectorChanged = false;
                // connector edited?
                if ($monitors[$connectorId]->getConnector()->getEdited() != $c->getEdited()) {
                    print_info("Connector settings changed for $connectorId");
                    $connectorChanged = true;
                }
                // filter has changed?
                else {
                    $lastFilterChange = $connectorService->lastFilterChange( $connectorId );
                    if ($monitors[$connectorId]->getConnector()->getField('last_filter_change') != $lastFilterChange) {
                        print_info("Filters changed for $connectorId");
                        $connectorChanged = true;
                    }
                }
                
                // connector changed? => stop
                if ($connectorChanged) {
                    print_info("Stopping monitor for: " . $c->getDescription() . " (changed)");
                    $monitors[$connectorId]->disconnect();
                    if (method_exists($monitors[$connectorId], 'setCallbackItemImported'))
                        $monitors[$connectorId]->setCallbackItemImported( null );
                    unset( $monitors[$connectorId] );
                }
            }
            
            // monitor not started? => start
            if (isset($monitors[$connectorId]) == false) {
                // get instance with all properties loaded
                $c = $connectorService->readConnector( $connectorId );
                
                // save edited-field last changed filter
                $lastFilterChange = $connectorService->lastFilterChange( $c->getConnectorId() );
                $c->setField('last_filter_change', $lastFilterChange);
                
                // connect
                if ($c->getConnectorType() == 'imap' || $c->getConnectorType() == 'horde') {
                    print_info("Starting monitor for: " . $c->getDescription());
                    $im = ImapConnector::createMailConnector( $c );
                    $im->setCallbackItemImported(function($folderName, $overview, $file, $changed) use ($c) {
                        // decode subject
                        $subject = imap_utf8( $overview->subject );
                        print_info("Importing mail, " . $c->getConnectorId() . ': ' . $subject . " (".$overview->date.")");
                        
                        // update solr
                        if (defined('WEBMAIL_SOLR') && WEBMAIL_SOLR) {
                            $solrImportMail = new SolrImportMail( WEBMAIL_SOLR );
                            $solrImportMail->queueEml( $file );
                            $solrImportMail->purge( true );
                        }
                    });
                    
                    $monitors[$connectorId] = $im;
                }
                else if ($c->getConnectorType() == 'pop3') {
                    // TODO: implement Pop3Monitor
                }
            }
        }
        
        
        
        // check for removed
        foreach($monitors as $connectorId => $monitor) {
            if (in_array($connectorId, $connectorIds) == false) {
                print_info("Removing monitor for: " . $monitor->getConnector()->getDescription());
                $monitors[$connectorId]->disconnect();
                if (method_exists($monitors[$connectorId], 'setCallbackItemImported'))
                    $monitors[$connectorId]->setCallbackItemImported( null );
                unset( $monitors[$connectorId] );
            }
        }
    }
    
    
    foreach($monitors as $cid => $monitor) {
        if ($monitor->poll()) {
            // TODO: fetch new mail
            print_info("Check it!");
            $monitor->import();
        }
    }
    
    
    
    sleep( 5 );
    
    $cnt = ($cnt+1) % (DEBUG?1:20);
}

// shouldn't be reached
print_info("Done.. :)");




