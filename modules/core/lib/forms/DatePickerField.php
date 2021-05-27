<?php

namespace core\forms;

class DatePickerField extends BaseWidget {
    
    protected $placeholder = false;
    
    protected $showWeeks = false;
    
    protected $options = array();
    
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    public function setShowWeeks($bln) { $this->showWeeks = $bln; }
    
    public function setOption($key, $val) { $this->options[$key] = $val; }
    public function getOption($key, $defaultValue=null) {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        else {
            return $defaultValue;
        }
    }
    
    public function getValue() {
        $v = parent::getValue();
        
        if (valid_date($v) == false && valid_datetime($v) == false) {
            return null;
        }
        
        $d = date2unix($v);
        if ($d)
            return date('Y-m-d', $d);
        
        return null;
    }
    
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget html-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span>'.esc_html(format_date($this->getValue(), 'd-m-Y')).'</span>';
        $html .= '</div>';
        
        return $html;
        
    }
    
    public function render() {
        
        $html = '';
        
        $this->setAttribute('type', 'text');
        
        $this->addContainerClass('datepicker-field-widget');
        $this->addContainerClass('widget-'. slugify($this->getName()));
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        if ($this->placeholder) {
            $this->setAttribute('placeholder', $this->getLabel());
        } else if (isset($this->options['placeholder']) && $this->options['placeholder']) {
            $this->setAttribute('placeholder', $this->options['placeholder']);
        }
        
        if (isset($this->options['readonly'])&&$this->options['readonly']) {
            $this->setAttribute('readonly', 'readonly');
        }
        
        if ($this->showWeeks) {
            $this->setAttribute('data-show-weeks', 1);
        }
        
        $this->setAttribute('class', 'input-pickadate reset-field-button');
        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'none'); 
        
        $v = '';
        if ($this->getValue()) {
            $t = date2unix($this->getValue());
            if ($t) {
                $v = date('d-m-Y', $t);
            }
        }
        
        $this->setAttribute('value', $v);
        
        return parent::render();
    }
    
    
}