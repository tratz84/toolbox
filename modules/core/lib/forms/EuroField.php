<?php

namespace core\forms;


class EuroField extends BaseWidget {
    
    protected $opts = array();
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->opts = $opts;
    }
    
    
    public function getValue() {
        return strtodouble(parent::getValue());
    }
    
    
    public function renderAsText() {
        $t = format_price($this->getValue(), true, ['thousands' => '.']);
        
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span>'.esc_html($t).'</span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
    
    public function render() {
        $t = format_price($this->getValue());
        
        $html = '';
        
        $html .= '<div class="widget euro-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).' </label>';
        $html .= '<input type="text" name="'.esc_attr($this->getName()).'" value="'.esc_attr($t).'" onchange="this.value=format_price(this.value, true)" />';
        $html .= '</div>';
        
        return $html;
    }
}

