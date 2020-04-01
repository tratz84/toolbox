<?php

namespace webmail\mail;

use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrImportMail;
use webmail\solr\SolrMail;
use core\exception\ObjectNotFoundException;

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
            $solrMail->setProperty('folder', 'Junk');
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
        if ($this->imapConnection->moveMailByUid($props->getUid(), $props->getFolder(), $if->getFolderName())) {
            $solrMail->setProperty('folder', $if->getFolderName());
            $solrMail->saveProperties();
            
            // update solr
            $su = new SolrImportMail( WEBMAIL_SOLR );
            $su->updateDoc($solrMail->getId(),
                [
                    'mailboxName' => $if->getFolderName()
            ]);
        }
    }
    
    
    public function saveEmailToConnector($connectorId, $emailId) {
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        /** @var \webmail\model\Connector $connector */
        $connector = $connectorService->readConnector( $connectorId );
        
        if (!$connector) {
            throw new ObjectNotFoundException('Email not found');
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
        
        $this->lastError = imap_last_error();
        
        return $r;
    }
    
    
    
}

