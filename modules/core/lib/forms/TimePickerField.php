<?php

namespace core\forms;

class TimePickerField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function getValue() {
        $v = parent::getValue();
        
        if (valid_time($v) == false) {
            return null;
        }
        
        if (strlen($v) == 8)
            $v = substr($v, 0, 5);
        
        return $v;
    }
    
    
    public function render() {
        $html = '';
        
        $v = $this->getValue();
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget timepicker-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input type="text" autocomplete=off inputmode="none" class="input-pickatime" name="'.esc_attr($this->getName()).'" value="'.esc_attr($v).'" />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}