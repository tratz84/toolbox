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
    
    
    public function setOptionItems( $optionItems ) { $this->optionItems = $optionItems; }
    public function getOptionItems() { return $this->optionItems; }
    
    
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
        
        $html .= '<div class="widget select-field-widget '.$extraClass.'">' . PHP_EOL;
        $html .= '<label>'.esc_html($this->getLabel()).'</label>' . PHP_EOL;
        $html .= '<select name="'.esc_attr($this->getName()).'">' . PHP_EOL;
        
        $html .= $this->renderOptions( $this->optionItems );
        
        $html .= '</select>' . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        
        return $html;
    }
    
    
    
    protected function renderOptions( $optionItems ) {
        $html = '';
        
        foreach($optionItems as $key => $val) {
            
            if (isset($val['optgroup']) && $val['optgroup']) {
                $html .= '<optgroup label="'.esc_attr($val['description']).'">'.PHP_EOL;
                if (isset($val['items'])) {
                    $html .= $this->renderOptions( $val['items'] );
                }
                $html .= '</optgroup>'.PHP_EOL;
            }
            else {
                if ((isset($val['active']) == false || $val['active']) || $key == $this->getValue()) {
                    $active = true;
                } else {
                    $active = false;
                }
                
                $html .= '<option value="'.esc_attr($key).'" style="'.($active?'':'display:none;').'" '.($key == $this->getValue()?'selected="selected"':'');
                
                // atributes set?
                if (isset($val['attributes']) && is_array($val['attributes'])) {
                    foreach( $val['attributes'] as $k => $v) {
                        $html .= ' ' . $k . '="' . esc_attr($v) . '" ';
                    }
                }
                
                $html .= '>';
                $html .= esc_html($val['description']);
                $html .= '</option>' . PHP_EOL;
            }
        }
        
        return $html;
    }
    
    
    
    
}