<?php

namespace core\event;

class CapabilityEvent {
    
    protected $user = null;
    
    protected $moduleName;
    protected $capabilityCode;
    
    protected $result = null;
    
    public function __construct($moduleName, $capabilityCode) {
        $this->moduleName = $moduleName;
        $this->capabilityCode = $capabilityCode;
    }
    
    public function setUser( $u ) { $this->user = $u; }
    public function getUser() { return $this->user; }
    
    public function setModuleName($n) { $this->moduleName = $n; }
    public function getModuleName() { return $this->moduleName; }
    
    public function setCapabilityCode($c) { $this->capabilityCode = $c; }
    public function getCapabilityCode() { return $this->capabilityCode; }
    
    public function setResult($r) { $this->result = $r; }
    public function getResult() { return $this->result; }
    public function hasResult() { return $this->result === null ? false : true; }
    
}
