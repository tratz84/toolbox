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
    
    
    public function updateTextSearch() {
        
        if (count($this->recipients)) {
            $tos = $this->recipients;
        } else {
            $etDao = object_container_get( EmailToDAO::class );
            $tos = $etDao->readByEmail( $this->getEmailId() );
        }
        
        $t = '';
        $t .= format_date( $this->getCreated(), 'Y_m_d' ) . "\n";
        $t .= format_date( $this->getCreated(), 'Y_m_d' ) . "\n";
        
        // full month name
        $monthNo = format_date( $this->getCreated(), 'm' );
        $yearNo  = format_date( $this->getCreated(), 'Y' );
        $t .= t('month.'.$monthNo) . ' ' . $yearNo . "\n";
        $t .= t('month-short.'.$monthNo) . ' ' . $yearNo . "\n";
        
        $t .= " " . $this->getFromName();
        $t .= " " . $this->getFromEmail();
        
        foreach($tos as $to) {
            $t .= ' ' . $to->getToName() ;
            $t .= ' ' . $to->getToEmail() ;
        }
        $t .= "\n";
        $t .= $this->getSubject();
        $t . "\n\n";
        
        $content = strip_tags( $this->getTextContent() );
        $content = preg_replace( '/\\s+/', ' ', $content );
        
        $t .= $content;
        
        $eDao = object_container_get( EmailDAO::class );
        $eDao->updateField( $this->getEmailId(), 'text_search', $t );
    }
    
    
    public function getAnswered() { return $this->isAnswered(); }
    public function isAnswered() {
        return $this->getAttributes() & \webmail\model\Email::ATTRIBUTE_REPLIED ? true : false;
    }
    
    public function getSeen() { return $this->isSeen(); }
    public function isSeen() {
        return $this->getAttributes() & \webmail\model\Email::ATTRIBUTE_SEEN ? true : false;
    }
    
    public function getJunk() { return $this->isJunk(); }
    public function isJunk() {
        return $this->getAttributes() & \webmail\model\Email::ATTRIBUTE_SPAM ? true : false;
    }
    
}

