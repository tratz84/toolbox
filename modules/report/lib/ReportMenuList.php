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
    
    
    public function getMenuItems() { return $this->menuItems; }
    
}

