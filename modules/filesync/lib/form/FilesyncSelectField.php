<?php


namespace filesync\form;

use core\forms\BaseWidget;


class FilesyncSelectField extends BaseWidget {
    
    protected $opts = array();
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        
        $this->setName($name);
        $this->setValue($value);
        $this->setLabel($label);
        $this->opts = $opts;
        
    }
    
    
    public function renderAsText() {
        $html = 'todo';
        
        return $html;
    }
    
    
    public function render() {
        $extraClass='';
        
        $html = '';
        $html .= '<div class="widget filesync-select-widget '.$extraClass.' widget-'.slugify($this->getName()).'">';
        
        $html .= '<input type="hidden" class="input-value" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" />';
        
        
        if ($this->getValue()) {
            // TODO: show preview
        }
        
        
        
        // buttons
        $html .= '<div class="filesync-select-field-buttons">';
            $html .= '<button class="btnNewFile">New file</button> ';
            $html .= '<button class="btnExistingFile">Existing file</button> ';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    
}


