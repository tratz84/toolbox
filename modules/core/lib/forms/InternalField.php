<?php

namespace core\forms;

class InternalField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function render() {
        return '';
    }
    
    public function renderAsText() {
        return '';
    }
    
    
}