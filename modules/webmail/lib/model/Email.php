<?php


namespace webmail\model;


class Email extends base\EmailBase {
    
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT  = 'sent';
    const STATUS_RECEIVED = 'received';
    
    const ATTRIBUTE_SEEN      = 0x01;
    const ATTRIBUTE_REPLIED   = 0x02;
    const ATTRIBUTE_FORWARDED = 0x04;
    const ATTRIBUTE_SPAM      = 0x10;
    const ATTRIBUTE_DELETED   = 0x20;
    

    protected $files = array();
    protected $recipients = array();
    
    public function __construct($id=null) {
        parent::__construct($id);
        
        $this->setConfidential( false );
    }
    
    
    
    public function setFiles($files) { $this->files = $files; }
    public function getFiles() { return $this->files; }
    
    // webmail__email_to
    public function setRecipients($p) { $this->recipients = $p; }
    public function getRecipients() { return $this->recipients; }
    public function addRecipient($p) { $this->recipients[] = $p; }

    
    public function getStatusAsText() {
        return t('webmail.status.' . $this->getStatus());
    }
    
    
    public function setField($key, $val) {
        // don't auto-fill created-field
        if ($key == 'created' && isset($this->fields['created']) && $this->fields['created']) {
            return;
        }
        
        return parent::setField($key, $val);
    }
    
}

