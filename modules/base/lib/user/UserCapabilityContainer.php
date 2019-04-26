<?php

namespace base\user;

class UserCapabilityContainer {
    
    protected $capabilities = array();
    
    public function __construct() {
        
    }
    
    
    public function addCapability($moduleName, $capabilityCode, $shortDescription, $infotext=null) {
        $this->capabilities[] = array(
            'module_name' => $moduleName,
            'capability_code' => $capabilityCode,
            'short_description' => $shortDescription,
            'infotext' => $infotext
        );
    }
    
    public function getCapabilities() {
        return $this->capabilities;
    }
    
}