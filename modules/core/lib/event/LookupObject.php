<?php

namespace core\event;

class LookupObject {
    
    protected $objectName;
    protected $id;
    protected $object = null;
    
    public function __construct($objectName, $id) {
        $this->setObjectName($objectName);
        $this->setId($id);
    }
    
    
    public function setObjectName($n) { $this->objectName = $n; }
    public function getObjectName() { return $this->objectName; }
    
    public function setId($i) { $this->id = $i; }
    public function getId() { return $this->id; }
    
    public function setObject($o) { $this->object = $o; }
    public function getObject() { return $this->object; }
    
    public function hasObject() {
        return $this->object === null ? false : true;
    }
    
    
    public function lookup() {
        hook_eventbus_publish($this, 'core', 'lookupobject');
        
        return $this->hasObject();
    }
    
}