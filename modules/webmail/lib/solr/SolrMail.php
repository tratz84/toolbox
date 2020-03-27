<?php


namespace webmail\solr;


use core\exception\OutOfBoundException;

class SolrMail {
    
    protected $jsonMail;
    
    protected $parsedMail = null;
    protected $mailIsParsed = false;
    
    protected $to  = null;
    protected $cc  = null;
    protected $bcc = null;
    
    
    protected $subject;
    protected $content;
    protected $contentHtml;
    protected $contentText;
    
    protected $attachments;
    
    protected $parserAttachments = array();
    
    
    public function __construct($jsonMail) {
        $this->jsonMail = $jsonMail;
        
    }

    public function getId() { return $this->jsonMail->id; }
    public function getEmlFile() { return $this->jsonMail->file; }
    public function getMailboxName() { return @$this->jsonMail->mailboxName; }
    public function getDate() {
        if (!@$this->jsonMail->date) {
            return null;
        }
        
        $dt = new \DateTime($this->jsonMail->date);
        $dt->setTimezone(new \DateTimeZone('Europe/Amsterdam'));
        
        return $dt->format('Y-m-d H:i:s');
    }
    
    public function getAttachments() { $this->parseMail(); return $this->attachments; }
    public function getAttachmentFile($fileno) {
        $this->parseMail();
        
        $fileno = (int)$fileno;
        
        if ($fileno < 0 || $fileno >= count($this->parserAttachments)) {
            throw new OutOfBoundException('Invalid attachment');
        }
        
        $pa = $this->parserAttachments[$fileno];
        
        $r = array();
        $r['filename'] = $pa->getFilename();
        $r['contentType'] = $pa->getContentType();
        $r['content'] = $pa->getContent();
        
        return $r;
    }
    
    public function getFromName() {
        return isset($this->jsonMail->fromName) ? $this->jsonMail->fromName : '';
    }
    public function getFromEmail() {
        return isset($this->jsonMail->fromEmail) ? $this->jsonMail->fromEmail : '';
    }
    
    public function getTo() { $this->parseMail();  return $this->to; }
    public function getCc() { $this->parseMail();  return $this->cc; }
    public function getBcc() { $this->parseMail(); return $this->bcc; }
    
    
    public function getSubject() {
        return isset($this->jsonMail->subject) ? $this->jsonMail->subject : '';
    }
    
    
    
    public function getContent() {
        $this->parseMail();
        if ($this->contentHtml) {
            return $this->contentHtml;
        } else {
            return nl2br( $this->contentText );
        }
        return $this->content;
    }
    public function getContentHtml() { $this->parseMail(); return $this->contentHtml; }
    public function getContentText() { $this->parseMail(); return $this->contentText; }
    
    
    public function getContentSafe() {
        $this->parseMail();
        
        // no contentHtml? => return contentText
        if (!$this->contentHtml || trim($this->contentHtml) == '') {
            return nl2br(esc_html($this->contentText));
        }
        
        // TODO: implement the other way around? not remove selective, but remove all BUT allowed tags.. ? (more safe approach & more future-proof?)
        
        // strip html
        $dom = new \DOMDocument();
        @$dom->loadHTML( '<?xml version="1.0 encoding="utf-8"?>'.$this->contentHtml );

        
        // remove comments
        $this->removeNodesByName( $dom->childNodes, array('#comment') );
        
        
        // remove elements
        $removeElements = array('script', 'style', 'link', 'base');
        foreach($removeElements as $re) {
            do {
                $els = $dom->getElementsByTagName( $re );
                if (count($els)) {
                    $els[0]->parentNode->removeChild( $els[0] );
                }
            } while (count($els) > 0);
        }
        
        // remove src=""-attributes
        $removeAttributes = array('src');
        $els = $dom->getElementsByTagName( '*' );
        foreach($els as $el) {
            foreach($removeAttributes as $ra) {
                if ($el->hasAttribute( $ra )) {
                    $el->removeAttribute($ra);
                }
            }
        }
        
        
        // anchors in new window & nofollow
        $els = $dom->getElementsByTagName( 'a' );
        foreach($els as $el) {
            $el->setAttribute('target', '_blank');
            $el->setAttribute('rel', 'nofollow');
        }
        
        return $dom->saveHTML();
    }
    
    protected function removeNodesByName( $childNodes, $nodeNames=array() ) {
        if (count($nodeNames) == 0) {
            return;
        }
        
        $cnt = count($childNodes)-1;
        for(; $cnt >= 0; $cnt--) {
            
            // remove?
            if ( in_array($childNodes[$cnt]->nodeName, $nodeNames) ) {
                $childNodes[$cnt]->parentNode->removeChild( $childNodes[$cnt] );
                continue;
            }
            
            if (isset($childNodes[$cnt]->childNodes) && count($childNodes[$cnt]->childNodes) > 0) {
                $this->removeNodesByName( $childNodes[$cnt]->childNodes, $nodeNames );
            }
        }
    }
    
    
    
    
    protected function parseMail( ) {
        // max parse once
        if ($this->mailIsParsed) {
            return;
        }
        
        $this->mailIsParsed = true;
        
        // fetch eml file
        $emlFile = get_data_file( $this->jsonMail->file );
        if (file_exists($emlFile) == false) {
            return false;
        }
        
        // parse
        $p = new \PhpMimeMailParser\Parser();
        $p->setPath($emlFile);
        
        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($p->getHeader('date')));
        
        $this->subject = $p->getHeader('subject');
        
        
        $this->attachments = array();
        $this->parserAttachments = $p->getAttachments(false);
        $pos=0;
        foreach($this->parserAttachments as $att) {
            $this->attachments[] = array(
                'pos'      => $pos,
                'filename' => $att->getFilename()
            );
            $pos++;
        }
        
        
        $this->to = $this->cc = $this->bcc = array();
        foreach($p->getAddresses('to') as $ht) {
            $this->to[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($p->getAddresses('cc') as $ht) {
            $this->cc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($p->getAddresses('bcc') as $ht) {
            $this->bcc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        
        $this->contentHtml = $p->getMessageBody('html');
        $this->contentText = $p->getMessageBody('text');
    }
    
    
}




