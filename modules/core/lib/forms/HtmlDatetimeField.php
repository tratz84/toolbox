<?php

namespace core\forms;

use core\Context;

class HtmlDatetimeField extends BaseWidget {
    
    protected $opts = array();
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->opts = $opts;
    }
    
    public function setOption($key, $val) {
        $this->opts[$key] = $val;
    }
    
    
    public function renderAsText() {
        $t = '';
        $dt = date2unix($this->getValue());
        
        if ($dt == null) {
            if (isset($this->opts['hide-when-invalid']) && $this->opts['hide-when-invalid'])
                return '';
        }
        
        return parent::renderAsText();
    }
    
    public function render() {
        
        $t = '';
        $dt = date2unix($this->getValue());
        
        if ($dt != null) {
            $t = date(Context::getInstance()->getDatetimeFormat(), $dt);
        } else {
            if (isset($this->opts['hide-when-invalid']) && $this->opts['hide-when-invalid'])
                return '';
        }
        
        
        
        $html = '';
        
        $html .= '<div class="widget html-field-widget">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= '<span>'.esc_html($t).'</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}