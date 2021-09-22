<?php

namespace core\forms;

class CallbackField extends BaseWidget {
    
    protected $callback = null;
    
    protected $showLabel = true;
    
    public function __construct($name, $callback=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue('');
        
        $this->callback = $callback;
    }
    
    public function setCallback($clb) { $this->callback = $clb; }
    
    public function setShowLabel($bln) { $this->showLabel = $bln; }
    public function enableLabel() { $this->showLabel = true; }
    public function disableLabel() { $this->showLabel = false; }
    
    
    
    public function render() {
        
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';

        // php 7.0 'fix'
        $callback = $this->callback;
        
        $html .= '<div class="widget callback-field-widget '.$extraClass.'">';
        
        if ($this->showLabel) {
            $html .= '<label>'.esc_html($this->getLabel()).'</label>';
        }
        
        $html .= $callback();
        $html .= '</div>';
        
        return $html;
    }
    
    
}
