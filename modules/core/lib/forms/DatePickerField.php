<?php

namespace core\forms;

class DatePickerField extends BaseWidget {
    
    protected $placeholder = false;
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    
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
        
        $this->setAttribute('class', 'input-pickadate reset-field-button');
        $this->setAttribute('autocomplete', 'off');
        
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