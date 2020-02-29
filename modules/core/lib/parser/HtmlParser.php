<?php


namespace core\parser;


class HtmlParser {
    
    protected $html = null;
    
    
    public function __construct() {
        
        
    }
    
    public function loadString($html) { $this->html = $html; }
    public function loadFile($file) { $this->html = file_get_contents($file); }
    
    
    public function parse() {
        
        $state = array();
        $state['in_tag'] = false;
        $state['pos'] = 0;
        
    }
    
    
}


