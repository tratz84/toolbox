<?php


namespace core\forms\container;


use core\forms\WidgetContainer;

class TableContainer extends WidgetContainer {
    
    protected $headerNames = array();
    protected $rows = array();
    
    public function __construct($name) {
        parent::__construct( $name );
        
    }
    
    
    public function setHeaderNames($names) {
        $this->headerNames = $names;
    }
    
    
    public function addRow($label, ... $args) {
        $this->rows[] = array(
            'label' => $label,
            'widgets' => $args
        );
        
        foreach($args as $w) {
            $this->addWidget( $w );
        }
    }
    
    
    public function render() {
        $html = '';
        
        $html .= '<table class="clear table-container">';
        $html .= '<thead>';
        $html .= '<tr>';
        foreach($this->headerNames as $n) {
            // TODO: escape value?
            $html .= '<td>'.$n.'</td>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        
        for($x=0; $x < count($this->rows); $x++) {
            $r = $this->rows[$x];
            
            $html .= '<tr>';
            $html .= '<td>'.esc_html($r['label']).'</td>';
            
            foreach($r['widgets'] as $w) {
                $html .= '<td>'.$w->render().'</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        
        return $html;
    }
    
    
}
