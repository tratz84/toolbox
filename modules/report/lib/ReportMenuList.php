<?php

namespace report;

use core\event\EventBus;

class ReportMenuList {
    
    
    protected $menuItems = array();
    
    public function __construct() {
        
    }
    
    
    public function triggerMenuEvent() {
        $eb = object_container_get(EventBus::class);
        $eb->publishEvent($this, 'report', 'menu-list');
        
    }
    
    public function addMenuItem($name, $module, $controllerName, $excelUrl=null) {
        $rmi = new ReportMenuItem($module, $name, $controllerName, $excelUrl);
        
        $this->menuItems[] = $rmi;
    }
    
    
    public function removeByControllerName( $controllerName ) {
        $this->menuItems = array_filter( $this->menuItems, function($mi) use ($controllerName) {
            if ($mi->getControllerName() == $controllerName) {
                return false;
            }
            else {
                return true;
            }
        });
        
    }
    
    public function setMenuItems( $menuItems ) { $this->menuItems = $menuItems; }
    public function getMenuItems() {
        usort($this->menuItems, function($rmi1, $rmi2) {
            return strcmp($rmi1->getName(), $rmi2->getName());
        });
        
        return $this->menuItems;
    }
    
}

