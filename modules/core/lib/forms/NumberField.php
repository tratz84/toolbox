<?php


namespace core\forms;


class NumberField extends BaseWidget {
    
    protected $placeholder = false;
    
    protected $min = '';
    protected $max = '';
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setValue($value) {
        parent::setValue( trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function setMin($m) { $this->min = $m; }
    public function setMax($m) { $this->max = $m; }
    
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $attributes = array();
        $attributes['type'] = 'number';
        $attributes['name'] = $this->getName();
        $attributes['value'] = $this->getValue();
        if ($this->placeholder)
            $attributes['placeholder'] = $this->getLabel();
        if (is_numeric($this->min))
            $attributes['min'] = $this->min;
        if (is_numeric($this->max))
            $attributes['max'] = $this->max;
                    
        
        
        
        $html .= '<div class="widget text-field-widget '.slugify($this->getName()).'-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input '.explode_attributes($attributes).' />';
        $html .= '</div>';
        
        return $html;
    }
}

