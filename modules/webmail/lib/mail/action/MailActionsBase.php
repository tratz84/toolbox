<?php


namespace webmail\mail\action;

use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use webmail\mail\SendMail;
use webmail\mail\connector\BaseMailConnector;
use webmail\model\Email;
use webmail\service\ConnectorService;
use webmail\service\EmailService;
use webmail\mail\actions\MysqlMailActions;


abstract class MailActionsBase {
    
    
    
    protected $mailConnector = null;
    
    protected $lastError = null;
    
    public function __construct() {
        
    }
    
    
    public static function getInstance() {
        
        $n = webmail_storage_engine();
        
        if ($n == 'solr') {
            return object_container_get( SolrMailActions::class );
        }
        else if ($n == 'db') {
            return object_container_get( MysqlMailActions::class );
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
    
    
}

