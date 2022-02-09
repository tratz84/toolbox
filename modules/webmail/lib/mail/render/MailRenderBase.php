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
        if ($this->emlViewer != null) {
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
            $this->properties = new MailProperties( $this->emlFile );
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
    
    
    public function getTo() { $this->parseMail();  return $this->to; }
    public function getCc() { $this->parseMail();  return $this->cc; }
    public function getBcc() { $this->parseMail(); return $this->bcc; }
    
    
    public function getRecipients() {
        $to  = $this->getTo();
        $cc  = $this->getCc();
        $bcc = $this->getBcc();
        
        return array_merge($to, $cc, $bcc);
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
    
    
    public function getParsedMail() { return $this->parsedMail; }
    
    
    public function parseMail( ) {
        // max parse once
        if ($this->mailIsParsed) {
            return;
        }
        
        $this->mailIsParsed = true;
        
        $this->getEmlViewer();
    }
}

