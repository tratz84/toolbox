<?php


namespace core\forms;



class FieldSetContainer extends WidgetContainer {
    
    public function __construct($name='widget-container', $label=null) {
        parent::__construct( $name );
        
        $this->setName( $name );
        $this->setLabel( $label );
    }
    
    
    
    public function render() {
        $this->sortWidgets();
        
        $html = '<fieldset class="widget widget-field-set-container widget-container-'.slugify($this->getName()).'">';
        
        $html .= '<legend>'.$this->getLabel().'</legend>';
        
        $html .= '<div class="fieldset-content">';
        foreach($this->widgets as $w) {
            $html .= $w->render();
        }
        $html .= '</div>';
        
        $html .= '</fieldset>';
        
        return $html;
    }
    
    
    
}

