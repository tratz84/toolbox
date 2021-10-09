<?php

namespace core\forms;

use core\forms\BaseWidget;

class Select2EditableField extends BaseWidget {
    
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
    
    
    public function hasSelectedOptionItem($optionItems = null, $depth=0) {
        if ($depth == 0) {
            $optionItems = $this->optionItems;
        }
        
        foreach( $optionItems as $key => $oi ) {
            if (isset($oi['optgroup']) && $oi['optgroup']) {
                $found = $this->hasSelectedOptionItem( $oi['items'], $depth+1 );
                if ($found)
                    return $found;
            }
            else {
                if ($key && $key == $this->value) {
                    return $oi;
                }
            }
        }
        
        return null;
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
        
        $extraClass .= 'widget-'.slugify($this->getName());
        
        $attrs = '';
        foreach($this->attributes as $k => $v) {
            $attrs .= ' '.$k.'="'.esc_attr($v).'" ';
        }
        
        $html .= '<div class="widget select2-editable-field-widget '.$extraClass.'" data-name="'.esc_attr($this->getName()).'">' . PHP_EOL;
        $html .= '<label>'.esc_html($this->getLabel()).'</label>' . PHP_EOL;
        
        $selectVisible = true;
        $inputVisible = true;
        
        if ($this->getValue() == '' || $this->hasSelectedOptionItem()) {
            $toggleClass = ' fa-pencil ';
            $name_select = $this->getName();
            $name_input = $this->getName() . '__disabled';
            $inputVisible = false;
        }
        else {
            $toggleClass = ' fa-times ';
            $name_select = $this->getName() . '__disabled';
            $name_input = $this->getName();
            $selectVisible = false;
        }
        
        $html .= '<select name="'.esc_attr($name_select).'" '.$attrs.' style="'.($selectVisible?'':'display: none;').'">' . PHP_EOL;
        $html .= $this->renderOptions( $this->optionItems );
        $html .= '</select>' . PHP_EOL;
        
        $html .= '<input type="text" name="' . esc_attr($name_input) . '" value="'.esc_attr($this->getValue()).'" ' . $attrs . ' style="'.($inputVisible?'':'display: none;').'" />';
        
        $html .= ' <a href="javascript:void(0);" class="fa '.$toggleClass.' select2-editable-toggle"></a>';
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