<?php


namespace webmail\mail\render;



use core\exception\DebugException;
use core\exception\NotImplementedException;
use webmail\model\ConnectorImapfolderDAO;

class MysqlMailRender extends MailRenderBase {
    
    /** @var \webmail\model\Email */
    protected $email = null;
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function setEmail( $email ) {
        $this->email = $email;
        
        $this->emlFile = $this->email->getSolrMailId();
    }
    
    public function getEmail() { return $this->email; }
    
    
    public function getId() { return $this->email->getSolrMailId(); }
    public function getEmlFile() { return $this->email->getSolrMailId(); }
    public function getEmlMessageId() { return $this->email->getMessageId(); }
    public function getEmlThreadId() {
        throw new NotImplementedException( 'not implemented' );
    }
    public function getRefMessageId() {
        throw new NotImplementedException( 'not implemented' );
    }
    
    
    public function getMailboxName() {
        $cifId = $this->email->getConnectorImapfolderId();
        $cifDao = new ConnectorImapfolderDAO();
        $cif = $cifDao->read($cifId);
        
        if ($cif) {
            return $cif->getFolderName();
        }
        else {
            return null;
        }
    }
    public function getDate() {
        return $this->email->getCreated();
    }
    
    public function getConnectorId() { return $this->email->getConnectorId(); }
    public function getUserId() { return $this->email->getUserId(); }
    public function getAction() { return $this->email->getAction(); }
    
    public function getAnswered() { return $this->isAnswered(); }
    public function isAnswered() {
        return $this->email->getAttributes() & \webmail\model\Email::ATTRIBUTE_REPLIED ? true : false;
    }
    public function getSeen() { return $this->isSeen(); }
    public function isSeen() {
        return $this->email->getAttributes() & \webmail\model\Email::ATTRIBUTE_SEEN ? true : false;
    }
    public function getJunk() { return $this->isJunk(); }
    public function isJunk() {
        return $this->email->getAttributes() & \webmail\model\Email::ATTRIBUTE_SPAM? true : false;
    }
    public function getForwarded() { return $this->isForwarded(); }
    public function isForwarded() {
        return $this->email->getAttributes() & \webmail\model\Email::ATTRIBUTE_FORWARDED? true : false;
    }
    
    public function hasFileAttachments() {
        return $this->email->getAttachmentCount();
    }
    
    
    
    public function getFromName() {
        return $this->email->getFromName();
    }
    public function getFromEmail() {
        return $this->email->getFromEmail();
    }
    
    public function getSubject() {
        return $this->email->getSubject();
    }
    
    
    public function parseMail( ) {
        // max parse once
        if ($this->mailIsParsed) {
            return;
        }
        

        $this->emlFile = $this->email->getSolrMailId();
        
        return parent::parseMail();
    }
}

