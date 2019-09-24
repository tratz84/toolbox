<?php
/**
 * FastsiteTemplateLoader - handling template files
 * 
 */

namespace fastsite\template;

use fastsite\data\FastsiteSettings;
use fastsite\exception\TemplateException;

class FastsiteTemplateLoader {
    
    protected $templateName;
    
    public function __construct($templateName=null) {
        if ($templateName == null) {
            $fs = object_container_get( FastsiteSettings::class );
            $templateName = $fs->getActiveTemplate();
        }
        
        if ($templateName == null) {
            throw new TemplateException('No template set');
        }
        
        $this->templateName = $templateName;
        
    }
    
    
    public function setTemplateName($n) { $this->templateName = $n; }
    public function getTemplateName() { return $this->templateName; }
    
    public function getFile($f) {
        $templateDir = get_data_file('fastsite/templates/'.$this->templateName);
        
        // template not found?
        if ($templateDir == false) {
            return false;
        }
        
        // get file
        $file = get_data_file('fastsite/templates/'.$this->templateName.'/'.$f);
        
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
        
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24))); // 24 hours
        header("Pragma: cache");
        header("Cache-Control: max-age=3600");
        header('Content-type: ' . file_mime_type($file));
        
        readfile( $file );
        
        return true;
    }
    
    
}
