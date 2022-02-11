<?php


namespace webmail\mail\render;

use core\exception\InvalidStateException;
use core\exception\OutOfBoundException;
use webmail\mail\MailProperties;
use webmail\mail\EmlViewer;




abstract class MailRenderBase {
    
    public const ACTION_OPEN       = 'open';
    public const ACTION_URGENT     = 'urgent';
    public const ACTION_INPROGRESS = 'inprogress';
    public const ACTION_REPLIED    = 'replied';
    public const ACTION_IGNORED    = 'ignored';
    public const ACTION_DONE       = 'done';
    public const ACTION_POSTPONED  = 'postponed';
    public const ACTION_PENDING    = 'pending';

    protected $to  = null;
    protected $cc  = null;
    protected $bcc = null;
    
    
    protected $subject;
    protected $content;
    protected $contentHtml;
    protected $contentText;
    
    protected $emlFile = null;
    protected $parsedMail = null;
    protected $mailIsParsed = false;
    
    protected $attachments;
    protected $parserAttachments = array();
    
    protected $properties = null;
    
    protected $changedFields = array();
    
    protected $emlViewer = null;
    
    public function __construct() {
        
    }
    
    public abstract function getId();
    
    
    public function getEmlFile() { return $this->emlFile; }
    
    public function getEmlViewer() {
        if ($this->emlViewer == null) {
            if (!$this->emlFile) {
                throw new InvalidStateException( 'emlFile not set' );
            }
            
            $f = get_data_file( $this->emlFile );
            if (file_exists($f) == false) {
                return false;
            }
            
            $this->emlViewer = new EmlViewer( $f );
            $this->emlViewer->parse();
        }
        
        return $this->emlViewer;
    }
    
    
    public function setChangedField( $fieldName, $val ) {
        $this->changedFields[$fieldName] = $val;
    }
    public function getChangedFields() { return $this->changedFields; }
    
    /**
     *
     * @return \webmail\mail\MailProperties
     */
    public function getProperties() {
        if ($this->properties === null) {
            $this->properties = new MailProperties( $this->getEmlFile() );
            $this->properties->load();
        }
        
        return $this->properties;
    }
    
    public function saveProperties() {
        return $this->properties->save();
    }
    
    
    public function getAttachments() { $this->parseMail(); return $this->attachments; }
    public function getAttachmentFile($fileno) {
        $this->parseMail();
        
        $patts = $this->emlViewer->getParserAttachments();
        
        $fileno = (int)$fileno;
        
        if ($fileno < 0 || $fileno >= count($patts)) {
            throw new OutOfBoundException('Invalid attachment');
        }
        
        $pa = $patts[$fileno];
        
        $r = array();
        $r['filename']    = $pa->getFilename();
        $r['contentType'] = $pa->getContentType();
        $r['content']     = $pa->getContent();
        
        return $r;
    }
    
    
    public function getTo() { $this->parseMail();  return $this->emlViewer->getTo(); }
    public function getCc() { $this->parseMail();  return $this->emlViewer->getCc(); }
    public function getBcc() { $this->parseMail(); return $this->emlViewer->getBcc(); }
    
    
    public function getRecipients() {
        $to  = $this->getTo();
        $cc  = $this->getCc();
        $bcc = $this->getBcc();
        
        return array_merge($to, $cc, $bcc);
    }
    
    
    
    public function getContent() {
        $this->parseMail();
        
        $h = $this->getContentHtml();
        if ($h) {
            return $h;
        } else {
            return nl2br( $this->getContentText() );
        }
    }
    public function getContentHtml() { $this->parseMail(); return $this->emlViewer->getContentHtml(); }
    public function getContentText() { $this->parseMail(); return $this->emlViewer->getContentText(); }
    
    
    public function getContentSafe() {
        $this->parseMail();
        
        return $this->emlViewer->getContentSafe();
    }
    
    
    public function getParsedMail() {
        return $this->emlViewer->getParser();
    }
    
    
    public function parseMail( ) {
        // max parse once
        if ($this->mailIsParsed) {
            return;
        }
        
        $this->mailIsParsed = true;
        
        $this->getEmlViewer();
    }
}

