<?php

namespace core\forms;

class CheckboxField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function getValue() {
        
        $v = parent::getValue();
        
        if ($v && ($v == 'on' || $v == '1'))
            return 1;
        else
            return 0;
        
    }
    
    public function renderTag() {
        $tag = parent::renderTag();
        $tag .= '<label class="checkbox-ui-placeholder" for="'.esc_attr($this->getName()).'"></label>';
        
        return $tag;
    }
    
    
    public function render() {
        $this->setAttribute('type', 'checkbox');
        $this->addContainerClass('checkbox-ui-container');
        
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        $this->setAttribute('id', $this->getName());
        
        if ($this->getValue()) {
           $this->setAttribute('checked', 'checked');
        }
        
        return parent::render();
    }
    
    
}