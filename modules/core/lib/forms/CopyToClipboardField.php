<?php

namespace core\forms;

class CopyToClipboardField extends BaseWidget {
    
    protected $escapeValue = true;
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function setEscapeValue($bln) { $this->escapeValue = $bln; }
    
    
    public function render() {
        $html = '';
        
        $html .= '<div class="widget widget-copy-to-clipboard widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        if ($this->escapeValue) {
            $html .= '<span class="value">'.esc_html($this->getValue()).'</span>';
        } else {
            $html .= '<span class="value">'.$this->getValue().'</span>';
        }
        
        $html .= ' <a class="fa fa-copy" href="javascript:void(0);" onclick="copyToClipboard( $(this).closest(\'.widget\').find(\'span.value\') );"></a>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}