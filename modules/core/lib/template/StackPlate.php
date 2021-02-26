<?php

namespace core\template;


class StackPlate {
    
    protected $templates = array();
    protected $vars = array();
    
    protected $usedIds = array();
    
    public function __construct() {
        
        
    }
    
    public function addTemplate($template, $prio=null) {
        if ($prio == null) {
            $prio = 10;
            foreach($this->templates as $t) {
                if ($prio <= $t['prio'])
                    $prio = $t['prio'] + 10;
            }
        }
        
        $this->templates[] = array('prio' => $prio, 'file' => $template);
    }
    
    public function setVars($vars) { $this->vars = $vars; }
    public function getVars() { return $this->vars; }
    
    public function setVar($key, $val) { $this->vars[$key] = $val; }
    public function getVar($key) { return $this->vars[$key]; }
    
    
    public function render() {
        usort($this->templates, function($t1, $t2) {
            if ($t1['prio'] == $t2['prio']) {
                return strcmp($t1['file'], $t2['file']);
            }
            
            if ($t1['prio'] > $t2['prio'])
                return 1;
            else
                return -1;
        });
        
        
        $dom = null;
        $x=0;
        foreach($this->templates as $t) {
            $dom = $this->renderTemplate($dom, $t['file']);
            
//             if ($x == 1) {
//             exit;
//             }
            
            $x++;
        }
        
        return $dom->saveHTML();
    }
    
    protected function renderTemplate($dom, $templatefile) {
        $html = $this->parseTemplate($templatefile);
        
        $subdom = new \DOMDocument('1.0', 'UTF-8');
        $subdom->xmlStandalone = false;
        $subdom->loadHTML($html);
        
        if ($dom == null) {
            $dom = $subdom;
        } else {
            $this->mergeDoms($dom, $subdom);
        }
        
        return $dom;
    }
    
    protected function parseTemplate($templatefile) {
        foreach($this->vars as $k => $v) {
            $$k = $v;
        }
        
        ob_start();
        
        include $templatefile;
        
        return ob_get_clean();
    }
    
    protected function mergeDoms($dom, $subdom) {
        /**
         * @var \DOMNodeList $rootElement
         */
        $rootElement = $subdom->getElementsByTagName('body');
        
        $rootNode = $rootElement->item(0);
        
        
        for( $x=0; $x < $rootNode->childNodes->length; $x++ ) {
            $node = $rootNode->childNodes[$x];
            
            if (!$node->attributes) continue;
            
            /**
             * @var \DOMElement $node 
             */
            $id = $node->getAttribute('id');
            if (!$id) continue;
            
            /**
             * @var \DOMNode $el
             */
            $el = $dom->getElementById($id);
            if ($el) {
                $newnode = $dom->importNode($node, true);
                
                
                $method = $node->getAttribute('insert-method');
                if ($method && $method == 'replace') {
                    $parent = $el->parentNode;
                    $parent->replaceChild($newnode, $el);
                }
                // default append
                else {
                    // don't use $dom->getElementById() to check, because that doesn't work on appended childs :S
                    for($cnt=1; in_array($id . '-child'.$cnt, $this->usedIds); $cnt++) {
                    }
                    $this->usedIds[] = $id . '-child'.$cnt;
                    
                    $newnode->setAttribute('id', $id . '-child'.$cnt);
                    $el->appendChild($newnode);
                }
            }
        }
    }
    
    
    
}


