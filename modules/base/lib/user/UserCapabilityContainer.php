<?php

namespace base\user;

class UserCapabilityContainer {
    
    protected $capabilities = array();
    
    public function __construct() {
        
    }
    
    
    public function addCapability($moduleName, $capabilityCode, $shortDescription, $infotext=null, $opts=array()) {
        $cap = $opts;
        
        $cap['module_name']       = $moduleName;
        $cap['capability_code']   = $capabilityCode;
        $cap['short_description'] = $shortDescription;
        $cap['infotext']          = $infotext;
        
        $this->capabilities[] = $cap;
    }
    
    public function getCapabilities() {
        return $this->capabilities;
    }
    
}