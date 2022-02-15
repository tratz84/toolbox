<?php


namespace webmail\mail\render;

use core\exception\DebugException;
use core\exception\OutOfBoundException;
use webmail\mail\MailProperties;



class SolrMailRender extends MailRenderBase {
    
    
    
    protected $jsonMail;
    
    
    public function __construct() {
        parent::__construct();
    }
    
    public function setJsonMail( $m ) {
        $this->jsonMail = $m;
    }
    
    public function getId() { return $this->jsonMail->id; }
    public function getSolrMailId() { return $this->jsonMail->id; }
    public function getEmlFile() { return $this->jsonMail->file; }
    public function getEmlMessageId() { return $this->jsonMail->emlMessageId; }
    public function getEmlThreadId() { return $this->jsonMail->emlThreadId; }
    public function getRefMessageId() { return $this->jsonMail->refMessageId; }
    
    
    public function getMailboxName() { return @$this->jsonMail->mailboxName; }
    public function getDate() {
        if (!@$this->jsonMail->date) {
            return null;
        }
        
        $dt = new \DateTime($this->jsonMail->date);
        $dt->setTimezone(new \DateTimeZone('Europe/Amsterdam'));
        
        return $dt->format('Y-m-d H:i:s');
    }
    
    public function getConnectorId() { return @$this->jsonMail->connectorId; }
    public function getUserId() { return @$this->jsonMail->userId; }
    public function getAction() { return @$this->jsonMail->action; }
    
    public function isAnswered() { return @$this->jsonMail->isAnswered; }
    public function isSeen() { return @$this->jsonMail->isSeen; }
    public function isJunk() { return @$this->jsonMail->isJunk; }
    
    public function hasFileAttachments() { return @$this->jsonMail->attachmentCount > 0 ? true : false; }
    
    
    
    public function getFromName() {
        return isset($this->jsonMail->fromName) ? $this->jsonMail->fromName : '';
    }
    public function getFromEmail() {
        return isset($this->jsonMail->fromEmail) ? $this->jsonMail->fromEmail : '';
    }
    
    public function getSubject() {
        return isset($this->jsonMail->subject) ? $this->jsonMail->subject : '';
    }
    
    
    public function parseMail( ) {
        // max parse once
        if ($this->mailIsParsed) {
            return;
        }
        
        // this should NEVER EVER happen. If so, it's a bug that must be immediately fixed.
        if (@$this->jsonMail->contextName != ctx()->getContextName()) {
            throw new DebugException('!!! E-mail in wrong context, fix immediately !!!');
        }
        
        $this->emlFile = $this->jsonMail->file;
        
        return parent::parseMail();
    }
    
    
}

