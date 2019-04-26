<?php

namespace core\forms;

class DoubleField extends BaseWidget {
    
    protected $placeholder = false;
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setValue($value) {
        parent::setValue( strtodouble($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $strPlaceholder = $this->placeholder ? 'placeholder="'.esc_attr($this->getLabel()).'"':'';
        
        $html .= '<div class="widget double-field-widget '.slugify($this->getName()).'-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input type="text" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" '.$strPlaceholder.' />';
        $html .= '</div>';
        
        return $html;
    }
    
    
}