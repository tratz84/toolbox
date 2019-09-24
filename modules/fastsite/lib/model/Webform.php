<?php


namespace fastsite\model;


class Webform extends base\WebformBase {

    protected $webformFields = array();
    
    public function __construct() {
        parent::__construct();
        
        $this->setActive(true);
    }
    
    public function setWebformFields($webformFields) {
        $this->webformFields = $webformFields;
    }
    public function getWebformFields() { return $this->webformFields; }
    public function addWebformField(WebformField $wf) { $this->webformFields[] = $wf; }
    

}

