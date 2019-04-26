<?php

namespace core\forms;

class PercentageField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function getValue() {
        return strtodouble( parent::getValue() );
    }
    
    
    public function render() {
        $v = format_percentage($this->getValue());
        
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget text-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<input type="text" class="autoformat-percentage" name="'.esc_attr($this->getName()).'" value="'.esc_attr($v).'" />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}