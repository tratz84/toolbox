<?php

namespace core\template;

use DOMDocument;

class DomPlate {
    
    protected $dom = null;
    
    public function __construct($html) {
        $this->dom = new DOMDocument();
        @$this->dom->loadHTML($html);
        
    }
    
    public function setByXPath($xpath, $html) {
        $x = new \DOMXPath($this->dom);
        
        $els = $x->query($xpath);
        foreach($els as $el) {
            $el->nodeValue = '';
            $template = $this->dom->createDocumentFragment();
            $template->appendXML( $html );
            
            $el->appendChild( $template );
        }
    }
    
    public function getHtml() {
        return $this->dom->saveHTML();
    }
    
}
