<?php

namespace fastsite\template;

use core\Context;
use core\exception\InvalidStateException;

class TemplatePageData {
    
    protected $data = array();
    protected $templateName;
    protected $filename;
    
    public function __construct($templateName, $filename) {
        $this->templateName = $templateName;
        $this->filename = $filename;
    }

    
    
    public function setName($n) { $this->setValue('name', $n); }
    public function getName() { return $this->getValue('name'); }
    
    
    
    public function getTemplateDir() {
        $pathParent = get_data_file('fastsite/templates');
        $pathTemplate = get_data_file('fastsite/templates/'.$this->templateName);
        
        if (strpos($pathTemplate, $pathParent) !== 0) {
            throw new InvalidStateException('Template-dir not found');
        }
        
        return $pathTemplate;
    }
    
    public function setValue($key, $val) { $this->data[$key] = $val; }
    public function getValue($key, $defaultValue=null) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        return $defaultValue;
    }
    
    
    public function save() {
        // determine data-dir
        $d = $this->getTemplatedir() . '/fastsite';
        if (is_dir($d) == false) {
            mkdir($d, 0755);
        }
        
        $filename = slugify($this->filename);
        $data = serialize($this->data);
        
        $r = file_put_contents($d . '/page-'.$filename, $data);
        
        return $r !== false;
    }
    
    public function load() {
        $d = $this->getTemplatedir() . '/fastsite';
        $filename = slugify($this->filename);
        
        $p = $d . '/page-' . $filename;
        if (file_exists($p)) {
            $data = @unserialize( file_get_contents( $p ));
            if ($data) {
                $this->data = $data;
                return true;
            }
        }
        
        return false;
    }
    
}

