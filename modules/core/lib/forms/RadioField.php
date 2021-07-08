<?php

namespace core\forms;

class RadioField extends BaseWidget {
    
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
        
        $html .= '<div class="widget radio-field-widget">';
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
        $extraClass .= ' ' . slugify($this->getName()).'-widget';
        
        $html .= '<div class="widget radio-field-widget '.$extraClass.'">';
        $html .= '<label class="radio-field-label">'.esc_html($this->getLabel()).'</label>';
        
        $html .= '<span class="radio-options-container">';
        foreach($this->optionItems as $key => $val) {
            $idslug = slugify($this->getName().'-'.$key);
            
            $html .= '<span class="radio-option-container">';
            $html .= '<input type="radio" class="radio-ui" id="'.esc_attr($idslug).'" name="'.$this->getName().'" value="'.esc_attr($key).'" '.($key == $this->getValue()?'checked="checked"':'').' /> ';
            $html .= '<label for="'.esc_attr($idslug).'" class="radio-ui-placeholder"></label> ';
            $html .= '<label class="widget-text" for="'.esc_attr($idslug).'" >'.esc_html($val).'</label> ';
            $html .= '</span>';
        }
        $html .= '</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}