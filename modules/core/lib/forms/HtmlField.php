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
        
        $val = $this->getValue();
        if ($val === null)
            $val = '';
        
        if (trim($val) == '' && isset($this->opts['hide-when-empty']) && $this->opts['hide-when-empty'])
            return '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<input type="hidden" name="'.esc_attr($this->getName()).'" value="'.esc_attr($val).'" />';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        if ($this->escapeValue) {
            $html .= '<span class="value">'.esc_html($val).'</span>';
        } else {
            $html .= '<span class="value">'.$val.'</span>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    
}