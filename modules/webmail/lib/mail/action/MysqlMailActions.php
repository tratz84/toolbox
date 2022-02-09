<?php


namespace webmail\mail\action;



use webmail\mail\SpamCheck;
use webmail\mail\action\MailActionsBase;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\solr\SolrImportMail;
use webmail\model\EmailDAO;
use webmail\mail\render\MysqlMailRender;
use webmail\model\EmailToDAO;



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
        $this->markMail($mail, '\\Seen');
        var_export($mail);exit;
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setSeen( true );
        $mailProperties->save();
        
        $this->updateSolrFields($solrMail->getId(),[ 'isSeen' => true ]);
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
    public function deleteByMailboxName($n)
    {}

    
    
    
}

