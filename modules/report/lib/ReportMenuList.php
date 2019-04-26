<?php

namespace report;

class ReportMenuList {
    
    
    protected $menuItems = array();
    
    public function __construct() {
        
    }
    
    public function addMenuItem($name, $module, $controllerName, $excelUrl=null) {
        $rmi = new ReportMenuItem($module, $name, $controllerName, $excelUrl);
        
        $this->menuItems[] = $rmi;
    }
    
    
    public function getMenuItems() { return $this->menuItems; }
    
}

