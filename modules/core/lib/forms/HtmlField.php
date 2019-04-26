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
    
    
    public function render() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<input type="hidden" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" />';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        if ($this->escapeValue) {
            $html .= '<span>'.esc_html($this->getValue()).'</span>';
        } else {
            $html .= '<span>'.$this->getValue().'</span>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    
}