<?php

namespace core\forms;

class HiddenField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        $extraClass .= ' hidden-field-widget-'.slugify($this->getName());
        
        $html .= '<div class="widget hidden-field-widget '.$extraClass.'">';
        $html .= '<input type="hidden" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" />';
        $html .= '</div>';
        
        return $html;
    }
    
    public function renderAsText() {
        return '';
    }
    
    
}