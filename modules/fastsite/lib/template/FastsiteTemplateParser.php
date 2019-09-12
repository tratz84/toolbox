<?php


namespace fastsite\template;


use fastsite\data\FastsiteTemplateFileSettings;
use fastsite\data\FastsiteTemplateSettings;

class FastsiteTemplateParser {
    
    protected $vars = array();
    
    /**
     * @var FastsiteTemplateSettings
     */
    protected $templateSettings;
    
    /**
     * @var FastsiteTemplateFileSettings
     */
    protected $tfs;
    
    public function __construct(FastsiteTemplateSettings $templateSettings, FastsiteTemplateFileSettings $tfs) {
        $this->templateSettings = $templateSettings;
        $this->tfs = $tfs;
    }
    
    
    public function addVars($vars) { $this->vars = array_merge($this->vars, $vars); }
    public function setVar($name, $val) { $this->vars[$name] = $val; }
    public function getVar($name, $defaultValue=null) {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }
        
        return $defaultValue;
    }
    
    
    protected function loadSnippet( $file ) {
        
        ob_start();
        foreach($this->vars as $key => $val) {
            $$key = $val;
        }
        
        include $file;
        
        return ob_get_clean();
        
    }
    
    
    protected function handleSnippets(\DOMDocument $dom) {
        // handle snippets
        $snippets = $this->tfs->getSnippets();
        foreach($snippets as $s) {
            $xpath = new \DOMXPath($dom);
            $elements = $xpath->query($s['xpath']);
            
            if ($elements->count()) {
                $snippetpath = @$this->templateSettings->getSnippetPath($s['snippet_name']);
                
                $html = $this->loadSnippet( $snippetpath );
                
                $frag = $dom->createDocumentFragment();
                
                $html = str_replace('&nbsp;', '&#160;', $html);
                
                $frag->appendXML( $html );
                
                $elements->item(0)->nodeValue = '';
                $elements->item(0)->appendChild( $frag );
            }
        }
    }
    
    protected function setDefaultMeta(\DOMDocument $dom) {
        
        $this->setTagContent($dom, '/html/head/title', $this->getVar('title'));
        
        $this->setAttributeValue($dom, "/html/head/meta[@name='description']", 'content', $this->getVar('meta_description'));
        $this->setAttributeValue($dom, "/html/head/meta[@name='keywords']", 'content', $this->getVar('meta_keywords'));
    }
    
    protected function setAttributeValue(\DOMDocument $dom, $xpath, $attributeName, $attributeValue) {
        $xpq = new \DOMXPath($dom);
        $els = $xpq->query( $xpath );
        
        if ($els->count()) {
            // set value
            $els->item(0)->setAttribute($attributeName, $attributeValue);
            return true;
        } else {
            // fetch parent
            $parentXpath = substr($xpath, 0, strrpos($xpath, '/'));
            $xpq = new \DOMXPath( $dom );
            $els = $xpq->query( $parentXpath );
            if ($els->count() == 0) {
                return false;
            }
            
            // determine nodename
            $nodeName = substr($xpath, strrpos($xpath, '/')+1);
            
            if (!$nodeName) {
                return false;
            }
            
            // check if attribute name/value is set in xpath
            $attrName2 = null;
            $attrValue2 = null;
            if (strpos($nodeName, '[') !== false) {
                $tail = substr($nodeName, strpos($nodeName, '['));
                $nodeName = substr($nodeName, 0, strpos($nodeName, '['));
                
                $tail = trim($tail, '[]@');
                list( $attrName2, $attrValue2 ) = explode('=', $tail, 2);
                $attrName2 = trim($attrName2, '\'"');
                $attrValue2 = trim($attrValue2, '\'"');
            }
            
            
            // create element, set values & append
            $node = $dom->createElement($nodeName);
            if ($attrName2) {
                $node->setAttribute($attrName2, $attrValue2);
            }
            $node->setAttribute($attributeName, $attributeValue);
            
            $els->item(0)->appendChild( $node );
            
            return true;
        }
    }
    
    
    protected function setTagContent(\DOMDocument $dom, $xpath, $content) {
        $xpq = new \DOMXPath($dom);
        $elements = $xpq->query( $xpath );
        
        if ($elements->count() == 0) {
            // add
            $parentXpath = substr($xpath, 0, strrpos($xpath, '/'));
            if ($parentXpath == '') $parentXpath = '/';
            
            // lookup parent
            $xpq = new \DOMXPath($dom);
            $elements = $xpq->query( $parentXpath );
            if ($elements->count()) {
                // append
                $elTitle = $dom->createElement('title');
                $elTitle->nodeValue = $content;
                $elements->item(0)->appendChild( $elTitle );
            }
            
        } else {
            // set
            $el = $elements->item(0);
            $el->nodeValue = $content;
        }
    }
    
    
    
    public function render() {
        $fth = object_container_get( FastsiteTemplateLoader::class );
        
        $html = file_get_contents( $fth->getFile($this->tfs->getFilename()) );
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        
        $this->setDefaultMeta($dom);
        
        // like it says :)
        $this->handleSnippets($dom);
        
        
        // TODO: handle default meta-stuff
        
        
        print $dom->saveHTML();
    }
    
}


