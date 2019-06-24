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
        $this->setAttribute('type', 'number');
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        if ($this->min)
            $this->setAttribute('min', $this->min);
        if ($this->max)
            $this->setAttribute('max', $this->max);
        
        
        if ($this->placeholder) {
            $this->setAttribute('placeholder', $this->getLabel());
        }
        
        $this->setAttribute('value', $this->getValue());
        
        return parent::render();
    }
}

