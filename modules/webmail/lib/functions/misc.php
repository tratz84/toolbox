<?php



use webmail\mail\action\MailActionsBase;
use webmail\mail\connector\BaseMailConnector;
use webmail\mail\render\MailRenderBase;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\storage\MailImportFactory;
use webmail\model\EmailDAO;
use webmail\model\EmailToDAO;
use core\db\DatabaseHandler;
use core\exception\InvalidStateException;


function lock_webmail_mail( $mail, $timeout = 60 ) {
    $id = null;
    
    if (is_a($mail, \webmail\mail\render\MysqlMailRender::class)) {
        $id = $mail->getId();
    }
    else if (is_array($mail) && isset($mail['id'])) {
        $id = $mail['id'];
    }

    if ($id == null) {
        throw new InvalidStateException( 'Unknown mail-object' );
    }
    
    $lockName = md5( $id );
    
    return db_lock( $lockName, $timeout );
}

function release_webmail_mail( $mail, $timeout = 60 ) {
    $id = null;
    
    if (is_a($mail, \webmail\mail\render\MysqlMailRender::class)) {
        $id = $mail->getId();
    }
    else if (is_array($mail) && isset($mail['id'])) {
        $id = $mail['id'];
    }
    
    $lockName = md5( $id );

    return db_release_lock( $lockName );
}


function webmail_storage_engine() {
    if (defined('WEBMAIL_SOLR')) {
        return 'solr';
    }
    else {
        return 'db';
    }
}



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
    $mapActions[ MailRenderBase::ACTION_OPEN ]      = t('Open');
    $mapActions[ MailRenderBase::ACTION_URGENT ]    = t('Urgent');
    $mapActions[ MailRenderBase::ACTION_INPROGRESS ]= t('In progress');
    $mapActions[ MailRenderBase::ACTION_POSTPONED ] = t('Postponed');
    $mapActions[ MailRenderBase::ACTION_DONE ]      = t('Done');
    $mapActions[ MailRenderBase::ACTION_REPLIED ]   = t('Replied');
    $mapActions[ MailRenderBase::ACTION_IGNORED ]   = t('Ignored');
    $mapActions[ MailRenderBase::ACTION_PENDING ]   = t('Pending');
    
    return $mapActions;
}


/**
 * webmail_import_folder() - import all eml-files in webmail/inbox-folder
 * 
 * @param boolean $updateOnly
 */
function webmail_import_folder($updateOnly) {
    
    $mi = MailImportFactory::getImportMail();
    $mi->setUpdateMode( $updateOnly );
    
    $mi->importFolder( ctx()->getDataDir().'/webmail/inbox' );
}

/**
 * webmail_import_connectors() - 
 * 
 * @param boolean $updateOnly
 */
function webmail_import_connectors($updateOnly, $opts=array()) {
    // loop through active Connectors to sync/fetch mail
    /** @var ConnectorService $connectorService */
    $connectorService = object_container_get( ConnectorService::class );
    $cs = $connectorService->readActive();
    
    foreach($cs as $c) {
        /** @var \webmail\model\Connector $c */
        $c = $connectorService->readConnector( $c->getConnectorId() );
        
        // save last update time for incremental updates
        $start_time_run = time();
        
        if ($c->getConnectorType() == 'imap' || $c->getConnectorType() == 'horde') {
            $ic = BaseMailConnector::createMailConnector($c);
            if (!$ic->connect()) {
                print_info("Unable to connect to " . $c->getDescription());
                continue;
            }
            
            if (is_cli()) {
                print_info("Connected to " . $c->getDescription());
            }
            
            // update only? just update messages from yesterday & today
            if ( $updateOnly ) {
                $ic->setSinceUpdate( date('Y-m-d', strtotime('-30 day')) );
            }
            
            $sentImapFolder =  $connectorService->readImapFolder( $c->getSentConnectorImapfolderId() );
            
            $mailImport = MailImportFactory::getImportMail();
            $mailImport->setUpdateMode( $updateOnly );
            
            $ic->setCallbackItemImported(function($folderName, $overview, $file, $changed) use ($mailImport, $sentImapFolder) {
                // this callback is only called on new mail and changed (IMAP-properties like isRead & folderName can change)
                //                 print "Queueing file: $file\n";
                if ($changed) {
                    $mailImport->queueEml( $file );
                    $mailImport->purge( );
                    
                    // new mail in Sent-folder? => mark In-Reply-To-mail as REPLIED if status is OPEN
                    if ($sentImapFolder && $sentImapFolder->getFolderName() == $folderName) {
                        // TODO: fix this...
                        $sma = MailActionsBase::getInstance();
                        $sma->autoMarkMessageAsReplied( $mailImport->getLastInReplyTo() );
                    }
                }
            });
                
            $ic->doImport( );
            
            if (isset($opts['inbox']) && $opts['inbox'])
                $ic->importInbox();
            
            $ic->disconnect();
            $ic->saveServerPropertyChecksums();
            
            $mailImport->purge( true );
        }
        else if ($c->getConnectorType() == 'pop3') {
            
        }
        
        // save time when run for Connector started, for incremental updates
        object_meta_save(Connector::class, $c->getConnectorId(), 'webmail_importall-lastrun', $start_time_run);
    }
}



function refresh_email_text_search( $updateOnly ){
    $eDao = new EmailDAO();
    
    $opts = array();
    if ($updateOnly)
        $opts['text_search_empty'] = true;
    
    /** @var \core\db\Cursor $cursor */
    $cursor = $eDao->search( $opts );
    
    $db = DatabaseHandler::getConnection('default');
    $db->beginTransaction();
    
    $total = $cursor->numRows();
    $cnt=1;
    
    /** @var \webmail\model\Email $e */
    while($e = $cursor->next()) {
        $e->updateTextSearch();
        
        if ($cnt%50 == 0) {
            print "Updating $cnt / $total\n";
        }
        
        $cnt++;
    }
    
    $db->commitTransaction();
}




function webmail_delete_connector_mail() {
    $db = DatabaseHandler::getConnection('default');
    
    $db->query('delete from webmail__email where connector_id is not null');
    $db->query('delete from webmail__email_to where email_id not in (select email_id from webmail__email)');
    
    print_cli_info( "Done" );
}




