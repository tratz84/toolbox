<?php

namespace core\forms;


class AudioField extends BaseWidget {
    
    protected $placeholder = false;
    protected $options;
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function setValue($value) {
        parent::setValue( $value === null ? null : trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $this->setAttribute('type', 'file');
        
        $this->setAttribute('accept', 'audio/*');
        $this->setAttribute('capture', 'capture');
        
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
