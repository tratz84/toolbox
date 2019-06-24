<?php

namespace core\forms;

class HtmlField extends BaseWidget {
    
    protected $escapeValue = true;
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setEscapeValue($bln) { $this->escapeValue = $bln; }
    
    public function renderTag() {
        $html = parent::renderTag();
        
        if ($this->escapeValue) {
            $html .= '<span>'.esc_html($this->getValue()).'</span>';
        } else {
            $html .= '<span>'.$this->getValue().'</span>';
        }
        return $html;
    }
    
        
    public function render() {
        $this->setAttribute('type', 'hidden');
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        $this->setAttribute('value', $this->getValue());
        
        return parent::render();
    }
    
    
}