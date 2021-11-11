<?php

namespace core\forms;

use core\ObjectContainer;
use core\template\HtmlScriptLoader;

class TinymceField extends BaseWidget {
    
    protected $fullpage = true;
    
    public function __construct($name, $value=null, $label=null) {
        $this->setName($name);
        $this->setValue($value);
        $this->setLabel($label);
        
        $hsl = ObjectContainer::getInstance()->get(HtmlScriptLoader::class);
        $hsl->enableGroup('tinymce');
    }
    
    /**
     * setFullpage() - add <html>, <head> & <body>-tags ?
     */
    public function setFullpage($bln) { $this->fullpage = $bln ? true : false; }
    
    
    public function renderAsText() {
        $html = '';
        
        // TODO: $this->getValue() contains safe html?
        
        $html .= '<div class="widget tinymce-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label class="widget-label">'.esc_html($this->getLabel()).'</label>';
        $html .= '<div>'.$this->getValue().'</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        
        $html .= '<div class="widget tinymce-field-widget widget-'.slugify($this->getName()).' '.$extraClass.'" data-fullpage="'.($this->fullpage?1:0).'">';
        $html .= '<label>' . esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<div><textarea class="input-tinymce" name="'.esc_attr($this->getName()).'">'.esc_html($this->getValue()).'</textarea></div>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}