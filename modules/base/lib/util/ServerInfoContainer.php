<?php

namespace base\util;


class ServerInfoContainer {
    
    
    protected $info = array();
    
    
    public function __construct() {
        
    }
    
    
    
    public function getInfo() { return $this->info; }
    
    public function addInfo($description, $value, $error=null) {
        
        $this->info[] = array(
            'description' => $description,
            'value'       => $value,
            'error'       => $error
        );
        
    }
    
    
    
    
    
}


