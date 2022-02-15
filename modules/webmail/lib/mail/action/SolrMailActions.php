<?php

namespace webmail\mail\action;

use webmail\mail\MailProperties;
use webmail\mail\SpamCheck;
use webmail\mail\connector\BaseMailConnector;
use webmail\model\Connector;
use webmail\service\ConnectorService;
use webmail\solr\SolrImportMail;
use webmail\solr\SolrMail;
use webmail\solr\SolrMailQuery;

class SolrMailActions extends MailActionsBase {
    
    
    
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
        $this->setMailFlags($solrMail, '\\Seen');
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setSeen( true );
        $mailProperties->save();
        
        $this->updateSolrFields($solrMail->getId(),[ 'isSeen' => true ]);
    }
    
    
    public function markAsAnswered(SolrMail $solrMail, $opts=array()) {
        $this->setMailFlags($solrMail, '\\Answered', $opts);
        
        // update property
        $mailProperties = $solrMail->getProperties();
        $mailProperties->setAnswered( true );
        
        // mark as replied
        if ($solrMail->getAction() == 'open') {
            $mailProperties->setAction('replied');
            
            // update solr fields
            $solrMail->setChangedField('action', 'replied');
            $solrFields = $solrMail->getChangedFields();
            
            $this->updateSolrFields($solrMail->getId(), $solrFields);
        }
        
        $mailProperties->save();
    }
    
    
    public function setMailFlags( $solrMail, $flag, $opts=array() ) {
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
        $mailConnector = BaseMailConnector::createMailConnector($connector);
        if ($mailConnector->connect()) {
            $mailConnector->setMailFlags($mailProperties->getUid(), $mailProperties->getFolder(), $flag);
            
            // Move-on-reply set?
            if (isset($opts['handle_reply']) && $opts['handle_reply'] && $connector->getReplyMoveImapfolderId()) {
                // fetch folder
                $targetIf = $connectorService->readImapFolder( $connector->getReplyMoveImapfolderId() );
                if ($targetIf && $targetIf->getFolderName() != $mailProperties->getFolder()) {
                    // move
                    $mailConnector->moveMailByUid($mailProperties->getUid(), $mailProperties->getFolder(), $targetIf->getFolderName());
                    
                    // mark field to be updated
                    $solrMail->setChangedField( 'mailboxName', $targetIf->getFolderName() );
                }
            }
            
//             $ic->expunge();
            $mailConnector->disconnect();
        }
    }
    
    
    
    public function moveMail(Connector $connector, $solrMail, $imapFolderId, $opts=array()) {
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
        
        $this->createMailConnector($connector);
        
        // try to connect
        if (!$this->mailConnector->isConnected()) {
            if ($this->mailConnector->connect() == false) {
                return false;
            }
        }
        
        // spam?
        if (isset($opts['spam']) && $opts['spam']) {
            $this->mailConnector->markJunk($props->getUid(),   $props->getFolder());
        
            $solrMail->getProperties()->setJunk(true);
        }
        
        // moved? => update properties-file
        if ($props->getUid() && $this->mailConnector->moveMailByUid($props->getUid(), $props->getFolder(), $if->getFolderName())) {
            $this->mailConnector->expunge();
            
            // moving mail is actually a copy- + delete-action. After a move
            // the UID of the message in mailbox must be updated
            $foundUids = $this->mailConnector->lookupUid($if->getFolderName(), $solrMail);
            $newUid = is_array($foundUids) && count($foundUids) == 1 ? $foundUids[0] : null;
            $solrMail->getProperties()->setUid( $newUid );
        }
        
        // move might fail if mail is already moved and mailbox is not in sink
        // just update solr? if mail is deleted, it's atleast in this mailbox in the right folder (especially in case of junk)
        // if this move is to the wrong folder, it will get synced automatically by modules/webmail/bin/webmail_importall.php-script
        
        $solrMail->getProperties()->setFolder( $if->getFolderName() );
        $solrMail->saveProperties();
        
        $this->updateFolder($solrMail->getId(), $if->getFolderName());
    }
    
    public function updateFolder($emailId, $folderName) {
        // update solr
        $su = new SolrImportMail( );
        $su->setSolrUrl( WEBMAIL_SOLR );
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
        $su = new SolrImportMail( );
        $su->setSolrUrl( WEBMAIL_SOLR );
        $su->updateDoc($emailId, $fields);
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
    
    public function deleteMail($mail) {
        $mp = new MailProperties($mail->getEmlFile());
        $mp->load();
        
        // delete mail from imap-server
        /** @var ConnectorService $connectorService */
        $connectorService = object_container_get(ConnectorService::class);
        
        $connector = $connectorService->readConnector($mail->getConnectorId());
        if ($connector && $connector->getConnectorType() == 'imap') {
            $this->createMailConnector($connector);
            
            // try to connect
            if ($this->mailConnector->isConnected() || $this->mailConnector->connect()) {
                
                // get trash-folder if available
                $trash_ifid = $connector->getTrashConnectorImapfolderId();
                $trash_if = null;
                if ($trash_ifid) {
                    $trash_if = $connectorService->readImapFolder($trash_ifid);
                }
                
                
                // trash-folder exists? => move message to trash
                if ($trash_if) {
                    $this->mailConnector->moveMailByUid($mp->getUid(), $mail->getMailboxName(), $trash_if->getFolderName());
                }
                // no trash-folder? => delete
                else {
                    $this->mailConnector->deleteMailByUid($mail->getMailboxName(), $mp->getUid());
                }
                
                $this->mailConnector->expunge();
                
                $this->mailConnector->disconnect();
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
        $sim->setSolrUrl( WEBMAIL_SOLR );
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
    
    
    public function deleteByMailboxName( $folderName, $connectorId=null ) {
        // solr-index + delete eml-files
        $smq = new SolrMailQuery();
        $smq->addFacetSearch('mailboxName', ':', $folderName);
        if ($connectorId)
            $smq->addFacetSearch('connectorId', ':', $connectorId);
        
        return $this->deleteSolrMailByQuery( $smq );
    }
    
}

