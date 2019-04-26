<?php


namespace webmail\model;


class Email extends base\EmailBase {
    
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT  = 'sent';

    protected $files;
    protected $recipients = array();
    
    public function setFiles($files) { $this->files = $files; }
    public function getFiles() { return $this->files; }
    
    // webmail__email_to
    public function setRecipients($p) { $this->recipients = $p; }
    public function getRecipients() { return $this->recipients; }
    public function addRecipient($p) { $this->recipients[] = $p; }

    
    public function getStatusAsText() {
        return t('webmail.status.' . $this->getStatus());
    }
}

