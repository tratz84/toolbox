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
use customer\model\CompanyEmailDAO;
use webmail\search\MysqlMailSearch;
use webmail\model\ConnectorImapfolderDAO;
use webmail\model\Email;



class MysqlMailActions extends MailActionsBase {
    
    public function markAsSpam( MysqlMailRender $mail ) {
        /** @var EmailService $eService */
        $eService = object_container_get( EmailService::class );
        
        
        $fullpathEmlFile = get_data_file( $mail->getEmlFile() );
        
        // eml not found?
        if (!$fullpathEmlFile)
            return false;
        
        // mark as spam
        SpamCheck::markSpam( $fullpathEmlFile );
        
        
        // if Connector exists, connection is imap & Junk-folder is set? => move eml file
        $mailProperties = $mail->getProperties();
        $connector = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
        }

        
        // EmailDAO
        $eDao = new EmailDAO();
        $eDao->updateField( $mail->getEmail()->getEmailId(), 'attributes', $mail->getEmail()->getAttributes() | Email::ATTRIBUTE_SPAM );
        $this->updateFolder( $mail->getId(), 'Junk' );
        
        // connector not found? => just throw in 'Junk'
        $mailProperties->setJunk(true);
        if (!$connector) {
            $mailProperties->setFolder('Junk');
            $mailProperties>saveProperties();
            
            //...
            return ['folder' => 'Junk'];
        }
        $mailProperties->save();
        
        if (in_array($connector->getConnectorType(), ['imap', 'horde']) && $connector->getJunkConnectorImapfolderId()) {
            $this->moveMail($connector, $mail, $connector->getJunkConnectorImapfolderId(), ['spam' => true]);
            
            $if = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
            
            if ($if) {
                $eDao = new EmailDAO();
                $eDao->updateField( $mail->getEmail()->getEmailId(), 'connector_imapfolder_id', $if->getConnectorImapfolderId() );
                $eDao->updateField( $mail->getEmail()->getEmailId(), 'folderName', $if->getFolderName() );
            }
            
            return ['folder' => $if->getFolderName()];
        }
        
        return true;
    }

    
    
    
    public function markAsHam( MysqlMailRender $mail ) {
        $fullpathEmlFile = get_data_file( $mail->getEmlFile() );
        
        // eml not found?
        if (!$fullpathEmlFile)
            return false;
        
        // mark as spam
        SpamCheck::markHam( $fullpathEmlFile );
        
        
        // if Connector exists, connection is imap & message is in Junk-folder? => move to inbox
        $mailProperties = $mail->getProperties();
        $connector = null;
        $junkImapFolder = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
            
            $junkImapFolder = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
        }
        
        // mark as non-junk
        $mailProperties->setJunk(false);
        $mailProperties->save();
        
        
        // no imap?
        if (!$connector || in_array($connector->getConnectorType(), array('horde', 'imap')) == false)
            return;
        if (!$junkImapFolder)
            return;
        
            
        // message not in junk-folder?
        if ($junkImapFolder && $junkImapFolder->getFolderName() != $mailProperties->getFolder()) {
            return;
        }
        
        // move message to INBOX
        $cifInbox = $connectorService->readImapfolderInbox( $connector->getConnectorId() );
        if ($cifInbox)
            $this->moveMail($connector, $mail, $cifInbox->getConnectorImapfolderId(), ['ham' => true]);
        
        $eDao = new EmailDAO();
        $eDao->updateField( $mail->getEmail()->getEmailId(), 'attributes', $mail->getEmail()->getAttributes() & ~Email::ATTRIBUTE_SPAM );
        
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
    
    public function markAsAnswered(MysqlMailRender $mail, $opts=array()) {
        $this->setMailFlags($mail, '\\Answered', $opts);
        
        // update property
        $mailProperties = $mail->getProperties();
        $mailProperties->setAnswered( true );
        
        // mark as replied
        if ($mail->getAction() == 'open') {
            $mailProperties->setAction('replied');
            
            // update solr fields
            $mail->setChangedField('action', 'replied');
            
            $this->updateAction($mail->getId(), 'replied');
            
            $changedFields = $mail->getChangedFields();
            if (isset($changedFields['mailboxName']) && $changedFields['mailboxName']) {
                $this->updateFolder( $mail->getId(), $changedFields['mailboxName'] );
            }
        }
        
        $mailProperties->save();
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
    
    
    /**
     * autoMarkMessageAsReplied() - mark message as 'REPLIED'. $emlMessageId is In-Reply-To-id of imported
     *                              mail. This function is used in modules/webmail/bin/webmail_importall.php
     */
    public function autoMarkMessageAsReplied($emlMessageId) {
        if (!$emlMessageId) {
            return false;
        }
        
        // get mail by messageId
        $ms = new MysqlMailSearch();
        $mail = $ms->readById( $emlMessageId );
        
        if ($mail) {
            // check if mail action == 'open', yes? => update to replied
            if ($mail->getAction() == MysqlMailRender::ACTION_OPEN) {
                $eDao = new EmailDAO();
                $eDao->updateField( $mail->getEmail()->getEmailId(), 'action', MysqlMailRender::ACTION_REPLIED );
                return true;
            }
        }
        
        return false;
    }
    
    public function updateFolder($emailId, $folderName) {
        // update solr
        $eDao = new EmailDAO();
        $mails = $eDao->readBySolrMailId( $emailId );
        
        if ( count($mails) ) {
            $emailId = $mails[0]->getEmailId();
            $eDao->updateField( $emailId, 'folderName', $folderName );
            
            // TODO: connector_imapfolder_id
            $connectorId = $mails[0]->getConnectorId();
            if ($connectorId) {
                $cifDao = new ConnectorImapfolderDAO();
                $cif = $cifDao->lookupFolder( $connectorId, $folderName );
                
                if ($cif) {
                    $eDao->updateField( $emailId, 'connector_imapfolder_id', $cif->getConnectorImapfolderId());
                }
            }
        }
        

    }
    
    
    public function deleteMail( $mail ) {
        $mp = $mail->getProperties();
        $mp->load();
        
        // delete mail from imap-server
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        
        $connector = $connectorService->readConnector($mail->getConnectorId());
        $trash_if = null;
        if ($connector && in_array($connector->getConnectorType(), ['imap', 'horde'])) {
            $this->createMailConnector($connector);
            
            // get trash-folder if available
            $trash_ifid = $connector->getTrashConnectorImapfolderId();
            if ($trash_ifid) {
                $trash_if = $connectorService->readImapFolder($trash_ifid);
            }
            
            // try to connect
            if ($this->mailConnector->isConnected() || $this->mailConnector->connect()) {
                
                
                
                // trash-folder exists? => move message to trash
                if ($trash_if) {
                    $this->mailConnector->moveMail($mail, $mail->getMailboxName(), $trash_if->getFolderName());
                }
                // no trash-folder? => delete
                else {
                    $this->mailConnector->deleteMail( $mail );
                }
                
                $this->mailConnector->expunge();
                
                $this->mailConnector->disconnect();
            }
        }
        
        
        // mark as deleted
        $mp->setMarkDeleted(true);
        
        // update db
        $email = $mail->getEmail();
        
        $attrs = $email->getAttributes() | \webmail\model\Email::ATTRIBUTE_DELETED;
        
        $eDao = new EmailDAO();
        $eDao->updateField( $email->getEmailId(), 'deleted', date('Y-m-d H:i:s') );
        $eDao->updateField( $email->getEmailId(), 'attributes', $attrs );
        if ($trash_if) {
            $eDao->updateField( $email->getEmailId(), 'folderName', $trash_if->getFolderName() );
            $mp->setFolder( $trash_if->getFolderName() );
        }
        else {
            $mp->setFolder( 'Trash' );
        }
        
        
        $mp->save();
    }
    
}

