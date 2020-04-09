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
use webmail\solr\SolrMailQuery;
use core\exception\InvalidStateException;

class SolrMailActions {
    
    protected $imapConnection = null;
    
    protected $lastError = null;
    
    public function __construct() {
        
    }
    
    
    public function setImapConnection($ic) { $this->imapConnection = $ic; }
    public function createImapConnection($connector) {
        if ($this->imapConnection != null) {
            if ($this->imapConnection->getConnector()->getConnectorId() != $connector->getConnectorId()) {
                throw new InvalidStateException('debug this! returning wrong ImapConnection');
            }
            
            return $this->imapConnection;
        }
        
        
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
            $solrMail->getProperties()->setJunk(true);
            $solrMail->saveProperties();
            
            // update solr
            $su = new SolrImportMail( WEBMAIL_SOLR );
            $su->updateDoc($solrMail->getId(), [ 'mailboxName' => 'Junk' ]);
            return;
        }
        
        
        if ($connector->getConnectorType() == 'imap' && $connector->getJunkConnectorImapfolderId()) {
            $this->moveMail($connector, $solrMail, $connector->getJunkConnectorImapfolderId(), ['spam' => true]);
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
        
        // mark as non-junk
        $mailProperties->setJunk(false);
        $mailProperties->save();
        
        
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
        
        $this->updateSolrFields($solrMail->getId(),[ 'isSeen' => true ]);
    }
    
    public function markAsAnswered(SolrMail $solrMail) {
        $this->markMail($solrMail, '\\Answered');
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setAnswered( true );
        
        // mark as replied
        if ($solrMail->getAction() == 'open') {
            $mailProperties->setAction('replied');
            $this->updateSolrFields($solrMail->getId(), ['action' => 'replied']);
        }
        
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
//             $ic->expunge();
            $ic->disconnect();
        }
    }
    
    
    
    public function moveMail(Connector $connector, SolrMail $solrMail, $imapFolderId, $opts=array()) {
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
        
        // spam?
        if (isset($opts['spam']) && $opts['spam']) {
            $this->imapConnection->setFlagByUid($props->getUid(),   $props->getFolder(), 'Junk');
            $this->imapConnection->setFlagByUid($props->getUid(),   $props->getFolder(), '$Junk');
            $this->imapConnection->clearFlagByUid($props->getUid(), $props->getFolder(), 'NonJunk');
            $this->imapConnection->clearFlagByUid($props->getUid(), $props->getFolder(), '$NonJunk');
        
            $solrMail->getProperties()->setJunk(true);
        }
        
        // moved? => update properties-file
        if ($props->getUid() && $this->imapConnection->moveMailByUid($props->getUid(), $props->getFolder(), $if->getFolderName())) {
            $this->imapConnection->expunge();
            
            // moving mail is actually a copy- + delete-action. After a move
            // the UID of the message in mailbox must be updated
            $foundUids = $this->imapConnection->lookupUid($if->getFolderName(), $solrMail);
            $newUid = count($foundUids) == 1 ? $foundUids[0] : null;
            $solrMail->getProperties()->setUid( $newUid );
        }
        
        // move might fail if mail is already moved and mailbox is not in sink
        // just update solr? if mail is deleted, it's atleast in this mailbox in the right folder (especially in case of junk)
        // if this move is to the wrong folder, it will get synced automatically by modules/webmail/bin/webmail_importall.php-script
        
        $solrMail->getProperties()->setFolder( $if->getFolderName() );
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
    
    
    /**
     * autoMarkMessageAsReplied() - mark message as 'REPLIED'. $emlMessageId is In-Reply-To-id of imported
     *                              mail. This function is used in modules/webmail/bin/webmail_importall.php
     */
    public function autoMarkMessageAsReplied($emlMessageId) {
        if (!$emlMessageId) {
            return false;
        }
        
        // get mail by messageId
        $smq = new SolrMailQuery();
        $smq->addFacetSearch('emlMessageId', ':', $emlMessageId);
        $smqr = $smq->search();
        
        if ($smqr->getNumFound() == 1) {
            $solrMail = $smqr->getMail(0);
            
            // check if mail action == 'open', yes? => update to replied
            if ($solrMail->getAction() == SolrMail::ACTION_OPEN) {
                $this->updateAction($solrMail->getId(), SolrMail::ACTION_REPLIED);
                return true;
            }
        }
        
        return false;
    }
    
    public function deleteMail(SolrMail $mail) {
        $mp = new MailProperties($mail->getEmlFile());
        $mp->load();
        
        // delete mail from imap-server
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        
        $connector = $connectorService->readConnector($mail->getConnectorId());
        if ($connector && $connector->getConnectorType() == 'imap') {
            $this->createImapConnection($connector);
            
            // try to connect
            if ($this->imapConnection->isConnected() || $this->imapConnection->connect()) {
                
                // get trash-folder if available
                $trash_ifid = $connector->getTrashConnectorImapfolderId();
                $trash_if = null;
                if ($trash_ifid) {
                    $trash_if = $connectorService->readImapFolder($trash_ifid);
                }
                
                
                // trash-folder exists? => move message to trash
                if ($trash_if) {
                    $this->imapConnection->moveMailByUid($mp->getUid(), $mail->getMailboxName(), $trash_if->getFolderName());
                }
                // no trash-folder? => delete
                else {
                    $this->imapConnection->deleteMailByUid($mail->getMailboxName(), $mp->getUid());
                }
                
                $this->imapConnection->expunge();
                
                $this->imapConnection->disconnect();
            }
        }
        
        
        // mark as deleted
        $mp->setMarkDeleted(true);
        $mp->save();
        
        // update solr
        $this->updateSolrFields($mail->getId(), ['markDeleted' => true]);
    }
    
    public function deleteSolrMail($id) {
        // delete solr index
        $sim = new SolrImportMail();
        $sim->delete('id:'.solr_escapePhrase($id));
        
        // delete eml file
        $emlfile = get_data_file_safe('webmail/inbox', substr($id, 14)); // 14 = length of '/webmail/inbox'
        if ($emlfile) {
            unlink( $emlfile );
            @unlink( $emlfile . '.sproperties' );
            @unlink( $emlfile . '.tbproperties' );
        }
        
        return true;
    }
    
    
    public function deleteSolrMailByQuery(SolrMailQuery $smq) {
        $deleteCount = 0;
        
        // delete all documents in response
        do {
            $r = $smq->search();
            $smq->setStart(0);
            
            // delete documents
            $docs = $r->getDocuments();
            foreach($docs as $doc) {
                $this->deleteSolrMail( $doc->id );
                
                $deleteCount++;
            }
            
            // loop till 0 results
        } while ($r->getNumFound() > 0);
        
        
        return $deleteCount;
    }
    
}

