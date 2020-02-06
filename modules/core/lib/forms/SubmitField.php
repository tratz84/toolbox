<?php


namespace core\forms;


class SubmitField extends BaseWidget {
    
    
    protected $placeholder = false;
    protected $options;
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        if ($label == null) {
            $label = t('Save');
        }
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    public function setValue($value) {
        parent::setValue( trim($value) );
    }
    
    public function showPlaceholder() { $this->placeholder = true; }
    
    public function render() {
        $this->setAttribute('type', 'submit');
        $this->setAttribute('name', $this->getName());
        
        if (isset($this->options['readonly'])&&$this->options['readonly']) {
            $this->setAttribute('readonly', 'readonly');
        }
        
        if (isset($this->options['disabled'])&&$this->options['disabled']) {
            $this->setAttribute('disabled', 'disabled');
        }
        
        $this->setAttribute('value', $this->getValue());
        
        return parent::renderTag();
    }
    
}
