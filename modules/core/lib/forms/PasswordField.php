<?php

namespace core\forms;

class PasswordField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setPlaceholder($str) { $this->options['placeholder'] = $str; }
    
    public function render() {
        
        $this->setAttribute('type', 'password');
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        if (isset($this->options['placeholder']) && $this->options['placeholder']) {
            $this->setAttribute('placeholder', $this->options['placeholder']);
        }
        
        if (isset($this->options['maxlength']) && is_numeric($this->options['maxlength'])) {
            $this->setAttribute('maxlength', $this->options['maxlength']);
        }
        if (isset($this->options['readonly'])&&$this->options['readonly']) {
            $this->setAttribute('readonly', 'readonly');
        }
        
        $this->setAttribute('autocomplete', 'new-password');
        
        $this->setAttribute('value', '');//$this->getValue());
        
        return parent::render();
    }
    
    
}