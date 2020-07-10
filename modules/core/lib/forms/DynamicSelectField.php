<?php

namespace core\forms;

class DynamicSelectField extends BaseWidget {
    
    protected $optionItems = array();
    
    protected $defaultText;
    protected $endpoint;
    
    public function __construct($name, $defaultValue, $defaultText, $endpoint, $label=null) {
        
        $this->setName($name);
        $this->setValue($defaultValue);
        $this->defaultText = $defaultText;
        $this->endpoint = $endpoint;
        $this->setLabel($label);
        
    }
    
    public function setDefaultText($t) { $this->defaultText = $t; }
    
    public function renderAsText() {
        $val = $this->getValue();
        
        if (isset($this->optionItems[$this->getValue()])) {
            $val = $this->optionItems[$this->getValue()];
        }
        
        $html = '';
        
        $html .= '<div class="widget select-field-widget">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<span>'.esc_html($this->defaultText).'</span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
    
    public function render() {
        $val = $this->getValue();
        
        $html = '';

        $extraClass = $this->hasError() ? 'error' : '';
        foreach( $this->containerClasses as $cc ) {
            $extraClass .= $cc;
        }
        
        $extraClass .= ' ' . slugify($this->getName()) . '-widget';
        
        $html .= '<div class="widget dynamic-select-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        
        $extraAttributes = '';
        foreach($this->attributes as $name => $val) {
            $extraAttributes .= esc_attr($name).'="'.esc_attr($val).'" ';
        }
        
        
        $html .= '<select name="'.esc_attr($this->getName()).'" class="select2-widget" data-url="'.esc_attr($this->endpoint).'" '.$extraAttributes.'>';
        if ($this->getValue() || $this->defaultText) {
            $html .= '<option value="'.esc_attr($this->getValue()).'">'.esc_html($this->defaultText).'</option>';
        }
        $html .= '</select>';
        
        
        $html .= '</div>';
        
        return $html;
    }
    
    
}