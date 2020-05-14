<?php

namespace core\forms;

class RawHtmlField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setEscapeValue($bln) { $this->escapeValue = $bln; }
    
    
    public function render() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<span class="value">'.$this->getValue().'</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}