<?php

namespace core\forms;


use core\Context;
use core\template\HtmlScriptLoader;
use core\ObjectContainer;
use core\forms\validator\HexColorValidator;

class ColorPickerField extends BaseWidget {
    
    protected $placeholder = false;
    protected $options;
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        ObjectContainer::getInstance()->get(HtmlScriptLoader::class)->enableGroup('jquery-colorpicker');
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function setValue($value) {
        parent::setValue( trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $strPlaceholder = $this->placeholder ? 'placeholder="'.esc_attr($this->getLabel()).'"':'';
        
        $extraAttributes = '';
        if (isset($this->options['maxlength']) && is_numeric($this->options['maxlength'])) {
            $extraAttributes .= ' maxlength="'.$this->options['maxlength'].'" ';
        }
        
        $val = $this->getValue();
        if (strpos($val, '#') === 0) {
            $val = substr($val, 1);
        }
        
        $style = '';
        if (HexColorValidator::validateHexColor($this->getValue())) {
            $style = 'background-color: #' . $val;
        }
        
        
        $html .= '<div class="widget color-picker-field-widget '.slugify($this->getName()).'-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<div class="color-selection-container">';
        $html .= '<span class="color-picker-color-sample" style="'.$style.'"></span>';
        $html .= '<input type="text" autocomplete=off name="'.esc_attr($this->getName()).'" value="'.esc_attr($val).'" ' . $strPlaceholder . ' ' . $extraAttributes . ' />';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}
