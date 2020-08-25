<?php

namespace core\forms;

class TextField extends BaseWidget {
    
    protected $placeholder = false;
    protected $options;
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function setValue($value) {
        parent::setValue( trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    public function setPlaceholder($str) { $this->options['placeholder'] = $str; }
    
    public function setAutocompleteUrl( $url ) { $this->setAttribute('autocomplete-url', $url ); }
    
    public function disableAutocomplete() { $this->setAttribute('autocomplete', 'off'); }
    
    public function render() {
        $this->setAttribute('type', 'text');
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        if ($this->placeholder) {
            $this->setAttribute('placeholder', $this->getLabel());
        } else if (isset($this->options['placeholder']) && $this->options['placeholder']) {
            $this->setAttribute('placeholder', $this->options['placeholder']);
        }
        
        if (isset($this->options['maxlength']) && is_numeric($this->options['maxlength'])) {
            $this->setAttribute('maxlength', $this->options['maxlength']);
        }
        if (isset($this->options['readonly'])&&$this->options['readonly']) {
            $this->setAttribute('readonly', 'readonly');
        }
        
        $this->setAttribute('value', $this->getValue());
        
        return parent::render();
    }
    
    
}