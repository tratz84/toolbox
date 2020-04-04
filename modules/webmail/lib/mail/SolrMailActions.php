<?php

namespace webmail\mail;

use core\exception\ObjectNotFoundException;
use function object_container_get;
use function webmail\mail\ImapConnection\imapAppend as imap_last_error;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrImportMail;
use webmail\solr\SolrMail;

class SolrMailActions {
    
    protected $imapConnection = null;
    
    protected $lastError = null;
    
    public function __construct() {
        
    }
    
    
    public function setImapConnection($ic) { $this->imapConnection = $ic; }
    public function createImapConnection($connector) {
        if ($this->imapConnection != null)
            return $this->imapConnection;
        
        $this->imapConnection = ImapConnection::createByConnector($connector);
    }
    
    public function closeConnection() {
        if ($this->imapConnection) {
            $this->imapConnection->disconnect();
            $this->imapConnection=null;
        }
    }
    
    public function getLastError() { return $this->lastError; }
    public function setLastError($v) { $this->lastError = $v; }
    
    
    public function markAsSpam(SolrMail $solrMail) {
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
            $solrMail->saveProperties();
            
            // update solr
            $su = new SolrImportMail( WEBMAIL_SOLR );
            $su->updateDoc($solrMail->getId(), [ 'mailboxName' => 'Junk' ]);
            return;
        }
        
        
        if ($connector->getConnectorType() == 'imap' && $connector->getJunkConnectorImapfolderId()) {
            $this->moveMail($connector, $solrMail, $connector->getJunkConnectorImapfolderId());
        }
        
    }

    
    public function markAsHam(SolrMail $solrMail) {
        $fullpathEmlFile = get_data_file( $solrMail->getEmlFile() );
        
        // eml not found?
        if (!$fullpathEmlFile)
            return false;
            
        // mark as spam
        SpamCheck::markHam( $fullpathEmlFile );
        
        
        // if Connector exists, connection is imap & message is in Junk-folder? => move to inbox
        $mailProperties = $solrMail->getProperties();
        $connector = null;
        $junkImapFolder = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
            
            $junkImapFolder = $connectorService->readImapFolder( $connector->getJunkConnectorImapfolderId() );
        }
        
        if (!$connector || $connector->getConnectorType() != 'imap')
            return;
        
        if (!$junkImapFolder)
            return;
        
        // message already not in junk-folder?
        if ($junkImapFolder && $junkImapFolder->getFolderName() != $mailProperties->getFolder())
            return;
        
        // move message to INBOX
        $this->moveMail($connector, $solrMail, 'INBOX');
    }
    
    
    public function markAsSeen(SolrMail $solrMail) {
        $this->markMail($solrMail, '\\Seen');
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setSeen( true );
        $mailProperties->save();
    }
    
    public function markAsAnswered(SolrMail $solrMail) {
        $this->markMail($solrMail, '\\Answered');
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setAnswered( true );
        $mailProperties->save();
    }
    
    
    public function markMail(SolrMail $solrMail, $flag) {
        // if Connector exists, connection is imap & message is in Junk-folder? => move to inbox
        $mailProperties = $solrMail->getProperties();
        $connector = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
        }
        
        if (!$connector || $connector->getConnectorType() != 'imap' || $connector->getActive() == false)
            return;
        
        
        // mark mail as answered
        $ic = ImapConnection::createByConnector($connector);
        if ($ic->connect()) {
            $ic->setFlagByUid($mailProperties->getUid(), $mailProperties->getFolder(), $flag);
            $ic->disconnect();
        }
    }
    
    
    
    public function moveMail(Connector $connector, SolrMail $solrMail, $imapFolderId) {
        // connector inactive?
        if ($connector->getActive() == false) {
            return false;
        }
        
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        /** @var \webmail\model\ConnectorImapfolder $if */
        $if = $connectorService->readImapFolder($imapFolderId);
        if (!$if)
            return false;
        
        $props = $solrMail->getProperties();
        
        // source same as destination?
        if ($props->getFolder() == $if->getFolderName())
            return false;
        
        $this->createImapConnection($connector);
        
        // try to connect
        if (!$this->imapConnection->isConnected()) {
            if ($this->imapConnection->connect() == false) {
                return false;
            }
        }
        
        // mark as spam
        $this->imapConnection->setFlagByUid($props->getUid(),   $props->getFolder(), 'Junk');
        $this->imapConnection->setFlagByUid($props->getUid(),   $props->getFolder(), '$Junk');
        $this->imapConnection->clearFlagByUid($props->getUid(), $props->getFolder(), 'NonJunk');
        $this->imapConnection->clearFlagByUid($props->getUid(), $props->getFolder(), '$NonJunk');
        
        // moved? => update properties-file
        if ($this->imapConnection->moveMailByUid($props->getUid(), $props->getFolder(), $if->getFolderName()) == false) {
            // ?
        }
        
        // move might fail if mail is already moved and mailbox is not in sink
        // just update solr? if mail is deleted, it's atleast in this mailbox in the right folder (especially in case of junk)
        // if this move is to the wrong folder, it will get synced automatically by modules/webmail/bin/webmail_importall.php-script
        
        $solrMail->getProperties()->setFolder('Junk');
        $solrMail->saveProperties();
        
        $this->updateSolrFolder($solrMail->getId(), $if->getFolderName());
    }
    
    public function updateSolrFolder($emailId, $folderName) {
        // update solr
        $su = new SolrImportMail( WEBMAIL_SOLR );
        $su->updateDoc($emailId,
            [
                'mailboxName' => $folderName
        ]);
    }
    
    public function updateAction($emailId, $action) {
        $this->updateSolrFields($emailId, ['action' => $action]);
    }

    protected function updateSolrFields($emailId, $fields) {
        // update solr
        $su = new SolrImportMail( WEBMAIL_SOLR );
        $su->updateDoc($emailId, $fields);
    }
    
    
    public function saveSendMail( SendMail $mail) {
        /** @var EmailService $emailService */
        $emailService = object_container_get(EmailService::class);
        
        /** @var \webmail\model\Identity $identity */
        $identity = $emailService->readIdentity( $mail->getIdentityId() );
        
        // connector linked to identity?
        if ($identity && $identity->getConnectorId()) {
            return $this->saveEmailToConnector($identity->getConnectorId(), $mail->getEmailId());
        }
        
        return false;
    }
    
    
    
    public function saveEmailToConnector($connectorId, $emailId) {
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        /** @var \webmail\model\Connector $connector */
        $connector = $connectorService->readConnector( $connectorId );
        
        if (!$connector) {
            throw new ObjectNotFoundException('Connector not found');
        }
        
        // fetch send-folder
        if (!$connector->getSentConnectorImapfolderId())
            return false;
        
        $if_send = $connectorService->readImapFolder($connector->getSentConnectorImapfolderId());
        if (!$if_send || $if_send->getFolderName() == '')
            return false;
        
        // get e-mail
        $emailService = object_container_get(EmailService::class);
        $email = $emailService->readEmail( $emailId );
   
        if (!$email) {
            throw new ObjectNotFoundException('Email not found');
        }
        
        $this->createImapConnection($connector);
        
        // connect to imap server
        if (!$this->imapConnection->isConnected()) {
            if ($this->imapConnection->connect() == false) {
                return false;
            }
        }
        
        // build eml-message
        $sendMail = SendMail::createMail($email);
        $emlMessage = $sendMail->buildMessage();
        
//         $emlMessage = str_replace("\n", "\r\n", $emlMessage);
//         print $emlMessage;exit; 
        
        $r = $this->imapConnection->imapAppend($if_send->getFolderName(), $emlMessage);
        
        $this->lastError = \imap_last_error();
        
        return $r;
    }
    
    
    
}

