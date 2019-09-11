<?php
/**
 * FastsiteTemplateFileSettings - configuration container for templatefile settings
 */

namespace fastsite\data;


class FastsiteTemplateFileSettings extends FileDataBase {
    
    protected $templateName;
    protected $filename;
    
    protected $data = array();
    
    public function __construct($templateName, $filename) {
        $this->templateName = $templateName;
        $this->filename = $filename;
    }
    
    
    
    public function setDescription($n) { $this->setValue('description', trim($n)); }
    public function getDescription() { return $this->getValue('description'); }
    
    public function setFilename($n) { $this->setValue('filename', $n); }
    public function getFilename() { return $this->getValue('filename'); }
    
    
    public function setSnippets($snippets) {
        return $this->setValue('snippets', $snippets);
    }
    
    
    public function getSnippets() {
        return $this->getValue('snippets', array());
    }
    
    
    public function save($f=null) {
        $d = $this->templateName . '/fastsite/page-'.slugify($this->filename).'.data';
        
        return parent::save($d);
    }
    
    public function load($f=null) {
        $d = $this->templateName . '/fastsite/page-'.slugify($this->filename).'.data';
        
        return parent::load($d);
    }
    
}

