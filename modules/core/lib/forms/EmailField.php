<?php

namespace core\forms;

class EmailField extends BaseWidget {
    
    protected $placeholder = false;
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $strPlaceholder = $this->placeholder ? 'placeholder="'.esc_attr($this->getLabel()).'"':'';
        
        $html .= '<div class="widget email-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<input type="email" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" '.$strPlaceholder.' />';
        $html .= '</div>';
        
        return $html;
    }
    
    
    
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span><a href="mailto:'.esc_attr($this->getValue()).'">'.esc_html($this->getValue()).'</a></span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
    
}