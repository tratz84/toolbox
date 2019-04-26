<?php

namespace core\event;

class PeopleEvent {
    
    protected $source;
    
    
    public static $STATE_UNKNOWN = -1;
    public static $STATE_BEFORE  = 1;
    public static $STATE_AFTER   = 2;
    
    protected $state;
    
    protected $moduleName;
    protected $actionName = "defaultAction";
    
    protected $message;
    
    public function __construct($source) {
        $this->source = $source;
        $this->state = self::$STATE_UNKNOWN;
    }
    
    
    public function getSource() { return $this->source; }
    
    public function getState() { return $this->state; }
    public function setState($state) { $this->state = $state; }
    
    
    public function getModuleName() { return $this->moduleName; }
    public function setModuleName($moduleName) { $this->moduleName = $moduleName; }
    
    public function getActionName() { return $this->actionName; }
    public function setActionName($actionName) { $this->actionName = $actionName; }
    
    
    public function getMessage() { return $this->message; }
    public function setMessage($message) { $this->message = $message; }
    
    
}