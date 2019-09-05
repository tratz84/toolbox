<?php


namespace fastsite;

class FastsiteTemplateHelper {
    
    protected $templateName;
    
    public function __construct($templateName=null) {
        $this->templateName = $templateName;
        
    }
    
    
    public function setTemplateName($n) { $this->templateName = $n; }
    public function getTemplateName() { return $this->templateName; }
    
    public function getFile($f) {
        $templateDir = get_data_file('fastsite/templates/'.$this->templateName);
        
        $file = get_data_file('fastsite/templates/'.$this->templateName.$f);
        
        if (strpos($file, $templateDir) !== 0) {
            return false;
        }
        
        if (is_dir($file)) {
            return false;
        }
        
        return $file;
    }
    
    public function serveFile($f) {
        
        $file = $this->getFile($f);
        
        if ($file == false) {
            return false;
        }
        
        header('Content-type: ' . file_mime_type($file));
        readfile( $file );
        
        return true;
    }
    
    
}
