<?php

namespace core\forms;

class DatePickerField extends BaseWidget {
    
    
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
            return date('Y-m-d', $d);
        
        return null;
    }
    
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span>'.esc_html(format_date($this->getValue(), 'd-m-Y')).'</span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
    public function render() {
        $html = '';
        
        $v = '';
        if ($this->getValue()) {
            $t = date2unix($this->getValue());
            if ($t) {
                $v = date('d-m-Y', $t);
            }
        }
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget datepicker-field-widget widget-'. slugify($this->getName()) . ' '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input type="text" autocomplete=off class="input-pickadate reset-field-button" name="'.esc_attr($this->getName()).'" value="'.esc_attr($v).'" />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}