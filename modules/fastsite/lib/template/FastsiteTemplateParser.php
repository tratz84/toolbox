<?php


namespace fastsite\template;


use fastsite\data\FastsiteTemplateFileSettings;
use fastsite\data\FastsiteTemplateSettings;
use fastsite\model\Webpage;

class FastsiteTemplateParser {
    
    protected $vars = array();
    
    /**
     * @var \DOMDocument
     */
    protected $dom;
    
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
    
    
    protected function handleSnippets() {
        // handle snippets
        $snippets = $this->tfs->getSnippets();
        foreach($snippets as $s) {
            $xpath = new \DOMXPath($this->dom);
            $elements = $xpath->query($s['xpath']);
            
            if ($elements && $elements->count()) {
                $snippetpath = @$this->templateSettings->getSnippetPath($s['snippet_name']);
                
                $html = $this->loadSnippet( $snippetpath );
                
                $frag = $this->dom->createDocumentFragment();
                
                $frag->appendXML( $this->escapeFragment($html) );
                
                $elements->item(0)->nodeValue = '';
                $elements->item(0)->appendChild( $frag );
            }
        }
    }
    
    protected function setDefaultMeta() {
        
        // header meta
        $this->setTagContent('/html/head/title', $this->getVar('title'));
        $this->setAttributeValue("/html/head/meta[@name='description']", 'content', $this->getVar('meta_description'));
        $this->setAttributeValue("/html/head/meta[@name='keywords']", 'content', $this->getVar('meta_keywords'));
        
        // page stuff
        if (($w = $this->getVar('webpage')) && is_a($w, Webpage::class)) {
            $c = $this->getAttributeValue('/html/body', 'class');
            $c = $c . ' webpage-'.$w->getWebpageId();
            
            $templateFile = basename($this->tfs->getFilename());
            $templateFile = substr($templateFile, 0, strrpos($templateFile, '.'));
            $c = $c . ' tpl-' . slugify($templateFile);
            $this->setAttributeValue('/html/body', 'class', $c);
        }
        
    }
    
    protected function getAttributeValue( $xpath, $attributeName, $defaultValue=null ) {
        $xpq = new \DOMXPath($this->dom);
        $els = $xpq->query( $xpath );
        
        if ($els->count() == 0) {
            return $defaultValue;
        }
        
        $el = $els->item(0);
        
        if ($el->hasAttribute($attributeName)) {
            return $el->getAttribute($attributeName);
        } else {
            return $defaultValue;
        }
    }
    
    protected function setAttributeValue( $xpath, $attributeName, $attributeValue) {
        $xpq = new \DOMXPath($this->dom);
        $els = $xpq->query( $xpath );
        
        if ($els->count()) {
            // set value
            $els->item(0)->setAttribute($attributeName, $attributeValue);
            return true;
        } else {
            // fetch parent
            $parentXpath = substr($xpath, 0, strrpos($xpath, '/'));
            $xpq = new \DOMXPath( $this->dom );
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
            $node = $this->dom->createElement($nodeName);
            if ($attrName2) {
                $node->setAttribute($attrName2, $attrValue2);
            }
            $node->setAttribute($attributeName, $attributeValue);
            
            $els->item(0)->appendChild( $node );
            
            return true;
        }
    }
    
    protected function insertFragment($xpath, $html) {
        $xp = new \DOMXPath($this->dom);
        
        $p = $xp->query($xpath);
        if ($p->count() == 0) {
            return false;
        }
        
        $frag = $this->dom->createDocumentFragment();
        $frag->appendXML( $this->escapeFragment($html) );
        
        $p->item(0)->appendChild($frag);
        
        return true;
    }
    
    protected function escapeFragment($html) {
        $html = str_replace('&quot;', '&#34;', $html);
        $html = str_replace('&amp;',  '&#38;', $html);
        $html = str_replace('&apos;', '&#39;', $html);
        $html = str_replace('&lt;', '&#60;', $html);
        $html = str_replace('&gt;', '&#62;', $html);
        $html = str_replace('&nbsp;', '&#160;', $html);
        $html = str_replace('&iexcl;', '&#161;', $html);
        $html = str_replace('&cent;', '&#162;', $html);
        $html = str_replace('&pound;', '&#163;', $html);
        $html = str_replace('&curren;', '&#164;', $html);
        $html = str_replace('&yen;', '&#165;', $html);
        $html = str_replace('&brvbar;', '&#166;', $html);
        $html = str_replace('&sect;', '&#167;', $html);
        $html = str_replace('&uml;', '&#168;', $html);
        $html = str_replace('&copy;', '&#169;', $html);
        $html = str_replace('&ordf;', '&#170;', $html);
        $html = str_replace('&laquo;', '&#171;', $html);
        $html = str_replace('&not;', '&#172;', $html);
        $html = str_replace('&shy;', '&#173;', $html);
        $html = str_replace('&reg;', '&#174;', $html);
        
        
        return $html;
    }
    
    
    protected function setTagContent($xpath, $content) {
        $xpq = new \DOMXPath($this->dom);
        $elements = $xpq->query( $xpath );
        
        if ($elements->count() == 0) {
            // add
            $parentXpath = substr($xpath, 0, strrpos($xpath, '/'));
            if ($parentXpath == '') $parentXpath = '/';
            
            // lookup parent
            $xpq = new \DOMXPath($this->dom);
            $elements = $xpq->query( $parentXpath );
            if ($elements->count()) {
                // append
                $elTitle = $this->dom->createElement('title');
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
        $this->dom = new \DOMDocument();
        @$this->dom->loadHTML($html);
        
        // handle default meta-stuff
        $this->setDefaultMeta();
        
        // like it says :)
        $this->handleSnippets();
        
        
        print $this->dom->saveHTML();
    }
    
}


