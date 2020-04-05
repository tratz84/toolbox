<?php

namespace core\forms;

class DateTimePickerField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function getValue() {
        $v = parent::getValue();
        
        if (valid_date($v) == false && valid_datetime($v) == false) {
            return null;
        }
        
        $d = date2unix($v);
        
        if ($d)
            return date('Y-m-d H:i:s', $d);
            
        return null;
    }
    
    
    public function render() {
        $html = '';
        
        $v = '';
        if ($this->getValue()) {
            $v = format_datetime($this->getValue(), 'd-m-Y H:i');
        }
        
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget datetimepicker-field-widget widget-'. slugify($this->getName()) . ' '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input type="text" autocomplete=off inputmode="none" class="input-pickadatetime" name="'.esc_attr($this->getName()).'" value="'.esc_attr($v).'" />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}