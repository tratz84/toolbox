<?php

namespace core\event;


class ActionValidationEvent extends PeopleEvent {

    protected $warnings = array();
    protected $errors = array();
    
    
    
    public function __construct($source, $moduleName, $actionName) {
        parent::__construct($source);
        
        $this->setModuleName($moduleName);
        $this->setActionName($actionName);
    }
    
    public function addWarning($msg) { $this->warnings[] = $msg; }
    public function getWarnings() { return $this->warnings; }
    public function hasWarnings() { return count($this->warnings) > 0; }
    
    
    public function addError($msg) { $this->errors[] = $msg; }
    public function getErrors() { return $this->errors; }
    public function hasErrors() { return count($this->errors) > 0; }
    
    
    
}