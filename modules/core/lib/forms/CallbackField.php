<?php

namespace core\forms;

class CallbackField extends BaseWidget {
    
    protected $callback = null;
    
    public function __construct($name, $callback=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue('');
        
        $this->callback = $callback;
    }
    
    
    public function render() {
        
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';

        // php 7.0 'fix'
        $callback = $this->callback;
        
        $html .= '<div class="widget callback-field-widget '.$extraClass.'">';
        $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        $html .= $callback();
        $html .= '</div>';
        
        return $html;
    }
    
    
}
