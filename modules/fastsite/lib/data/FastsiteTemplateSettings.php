<?php
/**
 * FastsiteTemplateSettings - configuration container for template settings
 */

namespace fastsite\data;


class FastsiteTemplateSettings extends FileDataBase {
    
    protected $templateName;
    
    public function __construct($templateName) {
        $this->templateName = $templateName;
    }
    
    
    
    public function setDefaultTemplateFile($f) { $this->setValue('default_template_file', $f); }
    public function getDefaultTemplateFile() { return $this->getValue('default_template_file'); }
    
    /**
     * getBaseTemplateFolder() - returns basefolder for template
     * 
     * @return string
     */
    public function getBaseTemplateFolder() {
        $f = $this->getDefaultTemplateFile();
        if (strpos($f, '/') !== false) {
            return dirname($f);
        }
        
        return '';
    }
    

    public function setTemplateFileProperty($filename, $key, $value) {
        $templatefiles = $this->getValue('templatefiles', array());
        
        if (isset($templatefiles[$filename]) == false) {
            $templatefiles[$filename] = array();
        }
        
        $templatefiles[$filename][$key] = $value;
    }
    
    public function registerTemplateFile($filename, $description) {
        $this->setTemplateFileProperty($filename, 'description', $description);
    }
    public function unregisterTemplateFile($filename) {
        $templatefiles = $this->getValue('templatefiles', array());
        if (isset($templatefiles[$filename])) {
            unset($templatefiles[$filename]);
            $this->setValue('templatefiles', $templatefiles);
        }
    }
    
    
    public function saveSnippet($name, $phpcode) {
        $name = basename($name);
        $path = $this->getTemplatesDir() . '/' . $this->templateName . '/fastsite/snippet-'.$name.'.php';
        
        return file_put_contents($path, $phpcode) !== false;
    }
    
    public function getSnippet($name) {
        $name = basename($name);
        $path = $this->getTemplatesDir() . '/' . $this->templateName . '/fastsite/snippet-'.$name.'.php';
        
        return file_get_contents($path) !== false;
    }
    
    
    
    public function save($f=null) {
        $d = $this->templateName . '/fastsite/settings.data';
        
        return parent::save( $d );
    }
    
    public function load($f=null) {
        $d = $this->templateName . '/fastsite/settings.data';
        
        return parent::load( $d );
    }
    
}

