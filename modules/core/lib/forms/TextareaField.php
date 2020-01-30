<?php

namespace core\forms;

class TextareaField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        $extraClass .= ' widget-'.slugify($this->getName());
        
        $html .= '<div class="widget textarea-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<textarea name="'.esc_attr($this->getName()).'">'.esc_html($this->getValue()).'</textarea>';
        $html .= '</div>';
        
        return $html;
    }
 
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget textarea-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<div>'.nl2br(esc_html($this->getValue())).'</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}