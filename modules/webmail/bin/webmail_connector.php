#!/usr/bin/php
<?php

/**
 * modules/webmail/bin/webmail_connector.php - monitors mailboxes for given contextname
 * 
 */


use core\ObjectContainer;
use webmail\mail\ImapMonitor;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrImportMail;
use webmail\model\Connector;

if (count($argv) != 2) {
    print "Usage: {$argv[0]} <contextname>\n";
    exit;
}

// bootstrap
chdir(__DIR__.'/../../../');
include 'config/config.php';

$contextName = $argv[1];
bootstrapCli($contextName);

// connect to database for current context
$dh = \core\db\DatabaseHandler::getInstance();
$dh->addServer('default', DEFAULT_DATABASE_HOST, DEFAULT_DATABASE_USERNAME, DEFAULT_DATABASE_PASSWORD, \core\Context::getInstance()->getCustomer()->getDatabaseName());


$connectorService = ObjectContainer::getInstance()->get(ConnectorService::class);

$monitors = array();

$cnt=0;
while (true) {
    
    if ($cnt == 0) {
        $connectors = $connectorService->readActive();

        if (count($connectors) == 0) {
            print "No active connectors\n";
        }
        
        // check if connectors are changed
        $connectorIds = array();
        /** @var Connector $c */
        foreach($connectors as $c) {
            $connectorId = $c->getConnectorId();
            $connectorIds[] = $connectorId;
            
            // monitor changed? => stop current
            if (isset($monitors[$connectorId])) {
                if ($monitors[$connectorId]->getConnector()->getEdited() != $c->getEdited()) {
                    print "Stopping monitor for: " . $c->getDescription() . " (changed)\n";
                    $monitors[$connectorId]->stop();
                    unset( $monitors[$connectorId] );
                }
            }
            
            // monitor not started? => start
            if (isset($monitors[$connectorId]) == false) {
                // get instance with all properties loaded
                $c = $connectorService->readConnector( $connectorId );
                
                if ($c->getConnectorType() == 'imap') {
                    print "Starting monitor for: " . $c->getDescription() . "\n";
                    $im = new ImapMonitor($c);
                    $im->setCallbackItemImported(function($folderName, $overview, $file) use ($c) {
                        print "Importing mail, " . $c->getConnectorId() . ': ' . $overview->subject . " (".$overview->date.")\n";
                        
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
                print "Removing monitor for: " . $monitor->getConnector()->getDescription() . "\n";
                $monitors[$connectorId]->stop();
                unset( $monitors[$connectorId] );
            }
        }
    }
    
    
    foreach($monitors as $cid => $monitor) {
        if ($monitor->poll()) {
            // TODO: fetch new mail
            print "Check it!\n";
            $monitor->import();
        }
    }
    
    
    
    sleep( 5 );
    
    $cnt = ($cnt+1) % (DEBUG?1:20);
}

// shouldn't be reached
print "Done.. :)\n";




