<?php


namespace webmail\mail;


use core\exception\FileException;
use core\exception\InvalidStateException;

class EmlViewer {
    
    protected $filename;
    
    protected $subject;
    protected $to;
    protected $cc;
    protected $bcc;
    protected $contentHtml;
    protected $contentText;
    
    protected $attachments;
    protected $parserAttachments;
    
    protected $parsed = false;
    
    
    public function __construct( $filename = null ) {
        $this->setFilename( $filename );
    }
    
    public function getFilename() { return $this->filename; }
    public function setFilename($n) { $this->filename = $n; }
    
    public function getSubject() { return $this->subject; }
    public function getTo() { return $this->to; }
    public function getCc() { return $this->cc; }
    public function getBcc() { return $this->bcc; }
    public function getContentHtml() { return $this->contentHtml; }
    public function getContentText() { return $this->contentText; }
    
    public function getAttachments() { return $this->attachments; }
    
    
    
    public function parse() {
        
        if (!$this->getFilename()) {
            throw new FileException('file not set');
        }
        if (is_file( $this->getFilename() ) == false) {
            throw new FileException( 'File not found' );
        }
        
        $parsedMail = new \PhpMimeMailParser\Parser();
        $parsedMail->setPath($this->getFilename());
        
        $dt = new \DateTime(null, new \DateTimeZone('+0000'));
        $dt->setTimestamp(strtotime($parsedMail->getHeader('date')));
        
        $this->subject = $parsedMail->getHeader('subject');
        
        $this->attachments = array();
        $this->parserAttachments = array();
        $tmpAttachments = $parsedMail->getAttachments();
        $pos=0;
        foreach($tmpAttachments as $att) {
            // inline? => skip
            //             if ($att->getContentID()) {
            //                 continue;
            //             }
                $this->attachments[] = array(
                    'pos'      => $pos,
                    'filename' => $att->getFilename()
                );
                $this->parserAttachments[] = $att;
                $pos++;
        }
        
        
        $this->to = $this->cc = $this->bcc = array();
        foreach($parsedMail->getAddresses('to') as $ht) {
            $this->to[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($parsedMail->getAddresses('cc') as $ht) {
            $this->cc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        foreach($parsedMail->getAddresses('bcc') as $ht) {
            $this->bcc[] = array(
                'name' => $ht['display'],
                'email' => $ht['address'],
            );
        }
        
        $this->contentHtml = $parsedMail->getMessageBody('html');
        $this->contentText = $parsedMail->getMessageBody('text');
        
        $this->parsed = true;
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
    
    public function getContentSafe() {
        if ($this->parsed == false) {
            throw new InvalidStateException('EmlViewer::parse() not called');
        }
        
        // no contentHtml? => return contentText
        if (!$this->contentHtml || trim($this->contentHtml) == '') {
            return nl2br(esc_html(trim($this->contentText)));
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
                else if ($el->nodeName == 'img' && $attributeName == 'src') {
                    $imgSrc = $val->nodeValue;
                    // inline image? => insert
                    $imgSet = false;
                    if (strpos($imgSrc, 'cid:') === 0) {
                        foreach( $this->parserAttachments as $pa ) {
                            if ($pa->getContentId() == substr($imgSrc, 4)) {
                                if (in_array( file_extension( $pa->getFilename() ), array('gif', 'png', 'jpg', 'jpeg') ) ) {
                                    $val->nodeValue = 'data:'.toolbox_mime_content_type( $pa->getFilename() ).';base64, '. base64_encode($pa->getContent());
                                    $imgSet = true;
                                }
                                break;
                            }
                            
                        }
                    }
                    
                    if ($imgSet == false) {
                        $el->removeAttribute($attributeName);
                    }
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
    
}

