<?php


namespace webmail\mail\action;



use webmail\mail\SpamCheck;
use webmail\mail\render\MysqlMailRender;
use webmail\model\EmailDAO;
use webmail\model\EmailToDAO;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\mail\MailProperties;
use core\exception\FileException;
use core\db\DatabaseHandler;



class MysqlMailActions extends MailActionsBase {
    
    public function markAsSpam( $mail ) {
        /** @var EmailService $eService */
        $eService = object_container_get( EmailService::class );
        
        $email = $eService->readEmail( $mail['email_id'] );
        
        $fullpathEmlFile = get_data_file( $solrMail->getEmlFile() );
        
        // eml not found?
        if (!$fullpathEmlFile)
            return false;
        
        // mark as spam
        SpamCheck::markSpam( $fullpathEmlFile );
        
        
        // if Connector exists, connection is imap & Junk-folder is set? => move eml file
        $mailProperties = $solrMail->getProperties();
        $connector = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
        }
        
        // connector not found? => just throw in 'Junk'
        if (!$connector) {
            $solrMail->getProperties()->setFolder('Junk');
            $solrMail->getProperties()->setJunk(true);
            $solrMail->saveProperties();
            
            // update solr
            $su = new SolrImportMail( );
            $su->setSolrUrl( WEBMAIL_SOLR );
            $su->updateDoc($solrMail->getId(), [ 'mailboxName' => 'Junk' ]);
            return ['folder' => 'Junk'];
        }
        
        
        if ($connector->getConnectorType() == 'imap' && $connector->getJunkConnectorImapfolderId()) {
            $this->moveMail($connector, $solrMail, $connector->getJunkConnectorImapfolderId(), ['spam' => true]);
            
            $if = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
            return ['folder' => $if->getFolderName()];
        }
        
        return true;
    }

    public function markAsSeen(MysqlMailRender $mail) {
        
        // update property
        $mailProperties = $mail->getProperties();
        $mailProperties->setSeen( true );
        $mailProperties->save();
        
        $this->setMailFlags($mail, '\\Seen');
        
        // update attributes-field
        $eDao = new EmailDAO();
        $attributes = $mail->getEmail()->getAttributes();
        $attributes |= \webmail\model\Email::ATTRIBUTE_SEEN;
        $eDao->setAttributes( $mail->getEmail()->getEmailId(), $attributes );
        
        $mail->getEmail()->setAttributes( $attributes );
    }
    
    
    public function readById( $id ) {
        
        $eDao = new EmailDAO();
        $e = $eDao->readBySolrMailId( $id );
        if (!$e) {
            return null;
        }
        
        $etDao = new EmailToDAO();
        $ets = $etDao->readByEmail( $e->getEmailId() );
        $e->setRecipients( $ets );
        
        
        $m = new MysqlMailRender();
        $m->setEmail( $e );
        
        return $m;
    }
    
    
    public function deleteByMailboxName( $folderName, $connectorId=null ) {
        
        $eDao = new EmailDAO();
        $etDao = new EmailToDAO();
        
        $dh = DatabaseHandler::getConnection('main');
        $dh->beginTransaction();
        
        // webmail__email_to-records
        $etDao->query("delete
                        from webmail__email_to
                        where email_id in (
                            select e.email_id
                            from webmail__email e
                            left join webmail__connector_imapfolder cif on (e.connector_imapfolder_id = cif.connector_imapfolder_id)
                            where folderName='Junk'
                                ".($connectorId?' and e.connector_id='.intval($connectorId):'')."
                        )
                ", array($folderName));
        
        // webmail__email-records
        $eDao->query("delete e
                        from webmail__email e
                        join webmail__connector_imapfolder cif on (e.connector_imapfolder_id = cif.connector_imapfolder_id)
                        where folderName=? 
                                ".($connectorId?' and e.connector_id='.intval($connectorId):'')
            , array($folderName));
        
        $dh->commitTransaction();
        
    }

    public function updateAction($emailId, $action) {
        $f = get_data_file( $emailId );
        $check = get_data_file('/webmail/inbox');
        if (strpos( $f, $check) !== 0) {
            throw new FileException( 'Invalid location' );
        }
        
        $mp = new MailProperties( $f );
        $mp->setAction( $action );
        $mp->save();
        
        
        $emailDao = object_container_get( EmailDAO::class );
        $emailDao->updateActionBySolrMailId( $emailId, $action );
    }
    
    
    
}

