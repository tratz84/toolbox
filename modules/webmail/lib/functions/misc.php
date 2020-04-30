<?php



use webmail\mail\ImapConnection;
use webmail\mail\SolrMailActions;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;
use webmail\solr\SolrMail;

function mapAllConnectors() {
    
    $connectorService = object_container_get(ConnectorService::class);
    $connectors = $connectorService->readConnectors();
    
    $map = array();
    $map[''] = t('Make your choice');
    foreach($connectors as $c) {
        $map[$c->getConnectorId()] = $c->getDescription()?$c->getDescription():$c->getConnectorId();
    }
    
    return $map;
}

function mapMailActions() {
    $mapActions = array();
    $mapActions[ SolrMail::ACTION_OPEN ]      = t('Open');
    $mapActions[ SolrMail::ACTION_URGENT ]    = t('Urgent');
    $mapActions[ SolrMail::ACTION_INPROGRESS ]= t('In progress');
    $mapActions[ SolrMail::ACTION_POSTPONED ] = t('Postponed');
    $mapActions[ SolrMail::ACTION_DONE ]      = t('Done');
    $mapActions[ SolrMail::ACTION_REPLIED ]   = t('Replied');
    $mapActions[ SolrMail::ACTION_IGNORED ]   = t('Ignored');
    $mapActions[ SolrMail::ACTION_PENDING ]   = t('Pending');
    
    return $mapActions;
}


/**
 * webmail_import_folder() - import all eml-files in webmail/inbox-folder
 * 
 * @param boolean $updateOnly
 */
function webmail_import_folder($updateOnly) {
    $solrImportMail = new SolrImportMail(WEBMAIL_SOLR);
    $solrImportMail->setUpdateMode( $updateOnly );
    $solrImportMail->importFolder( ctx()->getDataDir().'/webmail/inbox' );
}

/**
 * webmail_import_connectors() - 
 * 
 * @param boolean $updateOnly
 */
function webmail_import_connectors($updateOnly) {
    // loop through active Connectors to sync/fetch mail
    /** @var ConnectorService $connectorService */
    $connectorService = object_container_get( ConnectorService::class );
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
                $ic->setSinceUpdate( date('Y-m-d', strtotime('-30 day')) );
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
}


