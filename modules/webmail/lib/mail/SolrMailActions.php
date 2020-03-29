<?php

namespace webmail\mail;

use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;
use webmail\solr\SolrImportMail;

class SolrMailActions {
    
    protected $imapConnection = null;
    
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
        
        if (!$connector)
            return;
        
        if ($connector->getConnectorType() == 'imap' && $connector->getJunkConnectorImapfolderId()) {
            $this->moveMail($connector, $solrMail, $connector->getJunkConnectorImapfolderId());
        }
        
    }
    
    public function moveMail(Connector $connector, SolrMail $solrMail, $imapFolderId) {
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
                'mailboxName' => 'Junk'
            ]);
        }
        
        
        
    }
    
    
}

