<?php

namespace core\forms;

class FileField extends BaseWidget {
    
    protected $placeholder = false;
    protected $options;
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function setValue($value) {
        parent::setValue( trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $this->setAttribute('type', 'file');
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        if ($this->placeholder) {
            $this->setAttribute('placeholder', $this->getLabel());
        }
        
        $this->setAttribute('value', $this->getValue());
        
        return parent::render();
    }
    
    
}