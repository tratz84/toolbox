<?php

namespace core\forms;

class SelectField extends BaseWidget {
    
    protected $optionItems = array();
    protected $opts = array();
    
    public function __construct($name, $value=null, $optionItems=array(), $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setValue($value);
        $this->optionItems = $optionItems;
        $this->setLabel($label);
        $this->opts = $opts;
        
    }
    
    
    public function setOptionItems($i) { $this->optionItems = $i; }
    public function getOptionItems() { return $this->optionItems; }
    
    public function renderAsText() {
        $val = $this->getValue();
        
        if (isset($this->optionItems[$this->getValue()])) {
            $val = $this->optionItems[$this->getValue()];
        }
        
        $html = '';
        
        $html .= '<div class="widget select-field-widget">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<span>'.esc_html($val).'</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function getValueLabel() {
        $val = $this->getValue();
        
        if (isset($this->optionItems[$this->getValue()])) {
            $val = $this->optionItems[$this->getValue()];
        }
        
        return $val;
    }
    
    
    public function render() {
        if (isset($this->opts['add-unlisted']) && $this->opts['add-unlisted']) {
            if ($this->value !== null && isset($this->optionItems[$this->getValue()]) == false) {
                $this->optionItems[$this->getValue()] = $this->getValue();
            }
        }
        
        
        $html = '';

        $extraClass = $this->hasError() ? 'error' : '';
        
        $selectAttrs = '';
        foreach($this->attributes as $key => $val) {
            $selectAttrs .= ' ' . esc_attr($key).'="'.esc_attr($val).'" ';
        }
        
        $html .= '<div class="widget select-field-widget '.$extraClass.' widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<select '.$selectAttrs.' name="'.esc_attr($this->getName()).'">';
        
        
        foreach($this->optionItems as $key => $val) {
            $html .= '<option value="'.esc_attr($key).'" '.($key == $this->getValue()?'selected="selected"':'').'>'.esc_html($val).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}
