<?php


namespace core\forms;



use core\container\TabContainer;
use core\exception\InvalidStateException;

class TabWidgetContainer extends WidgetContainer {
    
    
    protected $tabContainer;
    
    protected $tabs = array();
    
    
    public function __construct($name='widget-container') {
        $this->setName($name);
        
        $this->tabContainer = new TabContainer( $name );
    }
    
    public function addTab( $name, $title, $prio=10, $opts=array() ) {
        $this->tabs[$name] = array(
            'title' => $title,
            'prio'  => $prio,
            'opts'  => $opts,
            'widgetNames' => array()
        );
    }
    
    public function addWidget($w) {
        $keys = array_keys( $this->tabs );
        if (count($keys) == 0)
            throw new InvalidStateException( 'No tabs defined' );
        
        // by default add to first tab
        $this->addTabWidget( $keys[0], $w );
    }
    
    public function addTabWidget( $tabName, $widget ) {
        $this->tabs[ $tabName ]['widgetNames'][] = $widget->getName();
        parent::addWidget( $widget );
    }
    
    
    public function render() {
        
        foreach($this->tabs as $name => $opts) {
            $widgetNames = $this->tabs[$name]['widgetNames'];
            
            $html = '';
            foreach($widgetNames as $wn) {
                $html .= $this->getWidget( $wn )->render();
            }
            
            $this->tabContainer->addTab( $opts['title'], $html, $opts['prio'], $opts);
        }
        
        return $this->tabContainer->render( ['return' => 1] );
    }
    
}


