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
                $frag->appendXML( $html );
                
                $elements->item(0)->nodeValue = '';
                $elements->item(0)->appendChild( $frag );
            }
        }
    }
    
    
    public function render() {
        $fth = object_container_get( FastsiteTemplateLoader::class );
        
        $html = file_get_contents( $fth->getFile($this->tfs->getFilename()) );
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        
        // like it says :)
        $this->handleSnippets($dom);
        
        
        // TODO: handle default meta-stuff
        
        
        print $dom->saveHTML();
    }
    
}


