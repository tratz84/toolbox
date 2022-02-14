<?php


namespace webmail\mail\action;

use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use webmail\mail\SendMail;
use webmail\mail\connector\BaseMailConnector;
use webmail\mail\render\MailRenderBase;
use webmail\model\Connector;
use webmail\model\Email;
use webmail\service\ConnectorService;
use webmail\service\EmailService;


abstract class MailActionsBase {
    
    
    protected $mailConnector = null;
    
    protected $lastError = null;
    
    public function __construct() {
        
    }
    
    
    public abstract function deleteByMailboxName( $folderName, $connectorId=null );
    
    
    public static function getInstance() {
        $n = webmail_storage_engine();
        
        if ($n == 'solr') {
            return new SolrMailActions();
        }
        else if ($n == 'db') {
            return new MysqlMailActions();
        }
        else {
            throw new InvalidStateException( 'engine not found' );
        }
        
    }
    
    
    public function createMailConnector($connector) {
        if ($this->mailConnector != null) {
            if ($this->mailConnector->getConnector()->getConnectorId() != $connector->getConnectorId()) {
                throw new InvalidStateException('debug this! returning wrong MailConnector');
            }
            
            return $this->mailConnector;
        }
        
        
        $this->mailConnector = BaseMailConnector::createMailConnector($connector);
    }
    
    public function closeConnection() {
        if ($this->mailConnector) {
            $this->mailConnector->disconnect();
            $this->mailConnector = null;
        }
    }
    
    public function getLastError() { return $this->lastError; }
    public function setLastError($v) { $this->lastError = $v; }
    
    
    
    
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
    
    
    /**
     * $opts - 'use-email-created-date' - when true, use $email->getCreated() as Date-header in e-mail
     */
    public function saveEmailToConnector($connectorId, $emailId, $opts=array()) {
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
        /** @var Email $email */
        $email = $emailService->readEmail( $emailId );
        
        if (!$email) {
            throw new ObjectNotFoundException('Email not found');
        }
        
        $this->createMailConnector($connector);
        
        // connect to imap server
        if (!$this->mailConnector->isConnected()) {
            if ($this->mailConnector->connect() == false) {
                return false;
            }
        }
        
        // build eml-message
        $sendMail = SendMail::createMail($email);
        $emlMessage = $sendMail->buildMessage();
        
        // use created-date of email for sent-datetime?
        if (isset($opts['use-email-created-date']) && $opts['use-email-created-date']) {
            $dt = new \DateTime($email->getCreated(), new \DateTimeZone('Europe/Amsterdam'));
            $emlMessage->setDate($dt);
        }
        
//         $emlMessage = str_replace("\n", "\r\n", $emlMessage);
//         print $emlMessage;exit;
        
        $r = $this->mailConnector->appendMessage($if_send->getFolderName(), $emlMessage);
        
        $this->lastError = \imap_last_error();
        
        return $r;
    }
    
    
    
    
    public function setMailFlags(MailRenderBase $mail, $flag, $opts=array()) {
        // if Connector exists, connection is imap & message is in Junk-folder? => move to inbox
        $mailProperties = $mail->getProperties();
        $connector = null;
        if ($mailProperties->getConnectorId()) {
            /** @var ConnectorService $connectorService */
            $connectorService = object_container_get(ConnectorService::class);
            /** @var \webmail\model\Connector $connector */
            $connector = $connectorService->readConnector( $mailProperties->getConnectorId() );
        }
        
        if (!$connector || in_array($connector->getConnectorType(), array('imap', 'horde')) == false || $connector->getActive() == false)
            return;
        
        // mark mail as answered
        $mailConnector = BaseMailConnector::createMailConnector($connector);
        if ($mailConnector->connect()) {
            $mailConnector->setMailFlags($mail, $mailProperties->getFolder(), $flag);
            
            // Move-on-reply set?
            if (isset($opts['handle_reply']) && $opts['handle_reply'] && $connector->getReplyMoveImapfolderId()) {
                // fetch folder
                $targetIf = $connectorService->readImapFolder( $connector->getReplyMoveImapfolderId() );
                if ($targetIf && $targetIf->getFolderName() != $mailProperties->getFolder()) {
                    // move
                    $mailConnector->moveMailByUid($mailProperties->getUid(), $mailProperties->getFolder(), $targetIf->getFolderName());
                    
                    // mark field to be updated
                    $mail->setChangedField( 'mailboxName', $targetIf->getFolderName() );
                }
            }
            
            //             $ic->expunge();
            $mailConnector->disconnect();
        }
    }
    
    
    
    public function moveMail(Connector $connector, MailRenderBase $mail, $imapFolderId, $opts=array()) {
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
        
        $props = $mail->getProperties();
        
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
            
            $mail->getProperties()->setJunk(true);
        }
        
        // moved? => update properties-file
        if ($this->mailConnector->moveMail($mail, $props->getFolder(), $if->getFolderName())) {
            $this->mailConnector->expunge();
            
            // moving mail is actually a copy- + delete-action. After a move
            // the UID of the message in mailbox must be updated
            $foundUids = $this->mailConnector->lookupUid($if->getFolderName(), $mail);
            $newUid = is_array($foundUids) && count($foundUids) == 1 ? $foundUids[0] : null;
            $mail->getProperties()->setUid( $newUid );
        }
        
        // move might fail if mail is already moved and mailbox is not in sink
        // just update solr? if mail is deleted, it's atleast in this mailbox in the right folder (especially in case of junk)
        // if this move is to the wrong folder, it will get synced automatically by modules/webmail/bin/webmail_importall.php-script
        
        $mail->getProperties()->setFolder( $if->getFolderName() );
        $mail->saveProperties();
        
        $this->updateFolder($mail->getId(), $if->getFolderName());
    }
}

