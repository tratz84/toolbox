<?php

namespace core\forms;

use core\forms\BaseWidget;

class Select2Field extends BaseWidget {
    
    protected $optionItems = array();
    protected $opts = array();
    
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
            $val = $this->optionItems[$this->getValue()]['description'];
        }
        
        $html = '';
        
        $html .= '<div class="widget select-field-widget">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<span>'.esc_html($val).'</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function getValueLabel($itemKey=null) {
        if ($itemKey === null) {
            $val = $this->getValue();
        } else {
            $val = $itemKey;
        }
        
        if (isset($this->optionItems[$val])) {
            $val = $this->optionItems[$val]['description'];
        }
        
        return $val;
    }
    
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget select-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<select name="'.esc_attr($this->getName()).'">';
        
        foreach($this->optionItems as $key => $val) {
            $html .= '<option value="'.esc_attr($key).'" style="'.($val['active']?'':'display:none;').'" '.($key == $this->getValue()?'selected="selected"':'').'>'.esc_html($val['description']).'</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}