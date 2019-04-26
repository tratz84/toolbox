<?php

namespace core\forms;

class PasswordField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget text-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<input type="password" autocomplete="new-password" name="'.esc_attr($this->getName()).'" value="" />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}