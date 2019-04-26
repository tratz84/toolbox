<?php

namespace core\forms;


class SelectImageTextField extends BaseWidget {
    
    public function __construct($name, $value=null, $optionItems=array(), $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setValue($value);
        $this->optionItems = $optionItems;
        $this->setLabel($label);
        $this->opts = $opts;
        
    }
    
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
    
    
    public function render() {
        if (isset($this->opts['add-unlisted']) && $this->opts['add-unlisted']) {
            if ($this->value !== null && isset($this->optionItems[$this->getValue()]) == false) {
                $this->optionItems[$this->getValue()] = $this->getValue();
            }
        }
        
        
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        $extraCss = isset($this->opts['css']) ? $this->opts['css'] : '';
        
        $html .= '<div class="widget select-image-text-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<select name="'.esc_attr($this->getName()).'" style="'.esc_attr($extraCss).'">';
        
        
        foreach($this->optionItems as $opt) {
            $html .= '<option data-image="'.esc_attr($opt['image']).'" value="'.esc_attr($opt['value']).'" '.($opt['value']== $this->getValue()?'selected="selected"':'').'>'.esc_html($opt['text']).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}
