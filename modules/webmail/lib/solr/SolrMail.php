<?php


namespace webmail\solr;


use core\exception\OutOfBoundException;
use webmail\mail\MailProperties;
use core\exception\DebugException;

class SolrMail {
    
    public const ACTION_OPEN       = 'open';
    public const ACTION_URGENT     = 'urgent';
    public const ACTION_INPROGRESS = 'inprogress';
    public const ACTION_REPLIED    = 'replied';
    public const ACTION_IGNORED    = 'ignored';
    public const ACTION_DONE       = 'done';
    public const ACTION_POSTPONED  = 'postponed';
    public const ACTION_PENDING    = 'pending';
    
    
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
    
    protected $properties = null;
    
    
    public function __construct($jsonMail) {
        $this->jsonMail = $jsonMail;
        
    }

    public function getId() { return $this->jsonMail->id; }
    public function getEmlFile() { return $this->jsonMail->file; }
    public function getEmlMessageId() { return $this->jsonMail->emlMessageId; }
    public function getEmlThreadId() { return $this->jsonMail->emlThreadId; }
    public function getRefMessageId() { return $this->jsonMail->refMessageId; }
    
    public function getParsedMail() { return $this->parsedMail; }
    
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
    
    public function getRecipients() {
        $to  = $this->getTo();
        $cc  = $this->getCc();
        $bcc = $this->getBcc();
        
        return array_merge($to, $cc, $bcc);
    }
    
    
    public function getSubject() {
        return isset($this->jsonMail->subject) ? $this->jsonMail->subject : '';
    }
    
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

        
        $allowedElements = array(
              '#text', 'html', 'body'
            , 'a', 'abbr', 'acronym', 'address', 'area', 'aside', 'b', 'bdi', 'big', 'blockquote', 'br', 'button'
            , 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'data', 'datalist', 'dd', 'del', 'details'
            , 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form'
            , 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'i', 'img', 'input', 'ins', 'kbd', 'keygen', 'label'
            , 'legend', 'li', 'main', 'map', 'mark', 'menu', 'menuitem', 'meter', 'nav', 'ol', 'optgroup', 'option'
            , 'output', 'p', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'small'
            , 'span', 'strike', 'strong', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th'
            , 'thead', 'time', 'tr', 'tt', 'u', 'ul', 'var', 'wbr', 'video'
        );
        
        $allowedAttributes = array(
            'abbr', 'accept', 'accept-charset', 'accesskey', 'action', 'align', 'alt', 'complete', 'autosave', 'axis'
            , 'bgcolor', 'border', 'cellpadding', 'cellspacing', 'challenge', 'char', 'charoff', 'charset', 'checked'
            , 'cite', 'clear', 'color', 'cols', 'colspan', 'compact', 'conteteditable', 'coords', 'datetime', 'dir'
            , 'disabled', 'draggable', 'dropzone', 'enctype', 'for', 'frame', 'headers', 'height', 'high', 'href'
            , 'hreflang', 'hspace', 'ismap', 'keytype', 'label', 'lang', 'list', 'longdesc', 'low', 'max', 'maxlength'
            , 'media', 'method', 'min', 'multiple', 'name', 'nohref', 'noshade', 'novalidate', 'nowrap', 'open'
            , 'optimum', 'pattern', 'placeholder', 'prompt', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required'
            , 'rev', 'reversed', 'rows', 'rowspan', 'rules', 'scope', 'selected', 'shape', 'size', 'span', 'spellcheck'
            , 'start', 'step', 'style', 'summary', 'tabindex', 'title', 'type', 'usemap', 'valign', 'value', 'vspace'
            , 'width', 'wrap', 'controls', 'class'
            // , 'src'
        );
        
        // filter nodes
        $this->allowNodesByName( $dom->childNodes, $allowedElements );
        
        
        // filter attributes
        $els = $dom->getElementsByTagName( '*' );
        foreach($els as $el) {
            $attrs = $el->attributes;

            for($x=$attrs->length-1; $x >= 0; $x--) {
                $val = $attrs->item($x);
                $attributeName = $val->nodeName;

                // style-attribute special case. Removal of url's is the most important
                if ($attributeName == 'style') {
                    $val->value = preg_replace('/url\(.*?\)/', '', $val->value);
                    
                    // remove all '<protocol>://' (trying to be future proof? :)
                    $val->value = preg_replace('/(\\S*):\\/\\/\\S*/', ';', $val->value);
                }
                // remove all not-allowed attrs
                else if (in_array($attributeName, $allowedAttributes) == false) {
                    $el->removeAttribute($attributeName);
                }
            }
        }
        
        
        // anchors in new window & nofollow
        $els = $dom->getElementsByTagName( 'a' );
        foreach($els as $el) {
            $el->setAttribute('target', '_blank');
            $el->setAttribute('rel', 'nofollow');
        }
        
        $body = $dom->getElementsByTagName('body');
        if ($body->count()) {
            $body = $body[0];
        } else {
            $body = null;
        }
        
        $html = $dom->saveHTML( $body );
        
        $html = preg_replace('/<body.*?>/', '', $html);
        $html = str_replace('</body>', '', $html);
        
        return $html;
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
    
    public function allowNodesByName( $childNodes, $allowedElements=array()) {
        $cnt = count($childNodes)-1;
        for(; $cnt >= 0; $cnt--) {
            // remove?
            // print "nodename: " . $childNodes[$cnt]->nodeName . "\n";
            
            if ( in_array($childNodes[$cnt]->nodeName, $allowedElements) == false ) {
                $childNodes[$cnt]->parentNode->removeChild( $childNodes[$cnt] );
                continue;
            }
            
            if (isset($childNodes[$cnt]->childNodes) && count($childNodes[$cnt]->childNodes) > 0) {
                $this->allowNodesByName( $childNodes[$cnt]->childNodes, $allowedElements );
            }
        }
        
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
        
        $this->mailIsParsed = true;
        
        // fetch eml file
        $emlFile = get_data_file( $this->jsonMail->file );
        if (file_exists($emlFile) == false) {
            return false;
        }
        
        // parse
        $this->parsedMail = new \PhpMimeMailParser\Parser();
        $this->parsedMail->setPath($emlFile);
        
        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($this->parsedMail->getHeader('date')));
        
        $this->subject = $this->parsedMail->getHeader('subject');
        
        
        $this->attachments = array();
	$this->parserAttachments = array();
	$tmpAttachments = $this->parsedMail->getAttachments();
        $pos=0;
        foreach($tmpAttachments as $att) {
            // inline? => skip
            if ($att->getContentID()) {
                continue;
            }
            $this->attachments[] = array(
                'pos'      => $pos,
                'filename' => $att->getFilename()
            );
	    $this->parserAttachments[] = $att;
            $pos++;
        }
        
        
        $this->to = $this->cc = $this->bcc = array();
        foreach($this->parsedMail->getAddresses('to') as $ht) {
            $this->to[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($this->parsedMail->getAddresses('cc') as $ht) {
            $this->cc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($this->parsedMail->getAddresses('bcc') as $ht) {
            $this->bcc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        
        $this->contentHtml = $this->parsedMail->getMessageBody('html');
        $this->contentText = $this->parsedMail->getMessageBody('text');
    }
    
    
}




