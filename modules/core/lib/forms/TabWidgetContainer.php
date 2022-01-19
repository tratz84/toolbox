<?php


namespace core\forms;



use core\container\TabContainer;
use core\exception\InvalidArgumentException;
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
            'widgets' => array()
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
        parent::addWidget( $widget );
        
        $this->tabs[ $tabName ]['widgets'][] = $widget;
    }
    
    
    public function render() {
        
        foreach($this->tabs as $name => $opts) {
            $html = '';
            
            foreach($this->tabs[$name]['widgets'] as $w) {
                $html .= $w->render() . PHP_EOL;
            }
            
            $this->tabContainer->addTab( $opts['title'], $html, $opts['prio'], $opts);
        }
        
        
        return $this->tabContainer->render( ['return' => 1]);
    }
    
}


